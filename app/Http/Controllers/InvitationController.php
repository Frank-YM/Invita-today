<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class InvitationController extends Controller
{
    public function show(Request $request, $slug = null)
    {
        if (!$slug) {
            return auth()->check()
                ? redirect()->route('admin')
                : redirect()->route('login');
        }

        $event = Event::where('slug', $slug)->firstOrFail();
        $isOwner = auth()->check() && auth()->id() === $event->user_id;
        $isPreview = $isOwner && $request->query('preview') == '1';

        if (!$event->is_published && !$isPreview) {
            abort(404);
        }

        $confirmed = $event->guests()->where('attending', true)->sum('companions')
                   + $event->guests()->where('attending', true)->count();
        $messages = $event->guests()->whereNotNull('message')->latest()->take(20)->get();

        return view('invitation', compact('event', 'confirmed', 'messages', 'isPreview'));
    }

    public function rsvp(Request $request)
    {
        $data = $request->validate([
            'event_id'   => 'required|exists:events,id',
            'name'       => 'required|string|max:100',
            'attending'  => 'required|boolean',
            'companions' => 'nullable|integer|min:0|max:10',
            'message'    => 'nullable|string|max:500',
        ]);

        $event = Event::findOrFail($data['event_id']);
        $isOwner = auth()->check() && auth()->id() === $event->user_id;
        if (!$event->is_published && !$isOwner) {
            abort(404);
        }

        $data['companions'] = $data['companions'] ?? 0;
        Guest::create($data);

        return redirect()->back()
            ->with('success', $data['attending']
                ? '¡Genial! Confirmamos tu asistencia 🎈'
                : 'Gracias por avisar, te extrañaremos 💛');
    }

    public function admin()
    {
        $user = auth()->user();
        $event = $user->events()->first();

        if (!$event) {
            $event = $user->events()->create([
                'title' => '¡Mi Cumpleaños!',
                'slug'  => $this->generateUniqueSlug('¡Mi Cumpleaños!'),
            ]);
        } elseif (!$event->slug) {
            $event->update([
                'slug' => $this->generateUniqueSlug($event->title, $event->id),
            ]);
        }

        $guests = $event->guests()->latest()->get();
        $totalPeople = $event->guests()->where('attending', true)->sum('companions')
                     + $event->guests()->where('attending', true)->count();

        return view('admin', compact('event', 'guests', 'totalPeople'));
    }

    private function generateUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'evento';
        $slug = $base;
        $n = 2;
        while (Event::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $n++;
        }
        return $slug;
    }

    /** Guarda los cambios del evento (título, fecha, ubicación por mapa) */
    public function updateEvent(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:120',
            'subtitle' => 'nullable|string|max:200',
            'emoji'    => 'nullable|string|max:8',
            'event_type' => 'nullable|string|max:30',
            'date'     => 'required|date',
            'place'    => 'required|string|max:200',
            'lat'      => 'required|numeric|between:-90,90',
            'lng'      => 'required|numeric|between:-180,180',
            'color_primary'   => 'required|string|max:20',
            'color_secondary' => 'required|string|max:20',
            'color_accent'    => 'required|string|max:20',
            'rsvp_button_text'=> 'nullable|string|max:60',
            'share_message'   => 'nullable|string|max:300',
            'dress_code'      => 'nullable|string|max:100',
            'gift_info'       => 'nullable|string|max:200',
            'extra_info'      => 'nullable|string|max:1000',
            'theme_character' => 'nullable|string|max:50',
            'template'        => 'nullable|string|max:30',
        ]);
        
        $event = auth()->user()->events()->firstOrFail();

        $data['is_published'] = $request->boolean('is_published');
        foreach (['show_countdown', 'show_map', 'show_confirmed_count', 'show_messages'] as $flag) {
            $data[$flag] = $request->has($flag) ? $request->boolean($flag) : $event->{$flag};
        }

        $event->update($data);

        return redirect()->route('admin')->with('saved', '✅ Evento actualizado correctamente');
    }

    public function togglePublish(Request $request)
    {
        $event = auth()->user()->events()->firstOrFail();
        $event->update(['is_published' => $request->boolean('publish')]);
        return back()->with('saved', $event->is_published
            ? '🎉 ¡Tu invitación está publicada!'
            : '📝 Invitación despublicada (volvió a borrador)');
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'slot'  => 'required|in:1,2',
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $event = auth()->user()->events()->firstOrFail();
        $slot = (int) $request->input('slot');
        $column = "photo_{$slot}";

        if ($event->{$column}) {
            Storage::disk('public')->delete($event->{$column});
        }

        $path = $request->file('photo')->store("events/{$event->id}", 'public');
        $event->update([$column => $path]);

        return back()->with('saved', '📸 Foto actualizada');
    }

    public function deletePhoto(Request $request)
    {
        $request->validate(['slot' => 'required|in:1,2']);

        $event = auth()->user()->events()->firstOrFail();
        $column = 'photo_' . (int) $request->input('slot');

        if ($event->{$column}) {
            Storage::disk('public')->delete($event->{$column});
            $event->update([$column => null]);
        }

        return back()->with('saved', '🗑️ Foto eliminada');
    }

    public function searchRevealImages(Request $request)
    {
        auth()->user()->events()->firstOrFail();

        $q = strtolower(trim((string) $request->query('q', '')));
        $dir = public_path('images/reveal');
        if (!is_dir($dir)) return response()->json([]);

        $files = collect(glob($dir . '/*.{png,jpg,jpeg,webp,gif}', GLOB_BRACE))
            ->map(function ($file) {
                $filename = basename($file);
                $stem = pathinfo($filename, PATHINFO_FILENAME);
                $parts = explode('_', $stem);
                $name = str_replace('-', ' ', array_shift($parts));
                return [
                    'name'  => $name,
                    'tags'  => array_map(fn($t) => str_replace('-', ' ', $t), $parts),
                    'path'  => 'images/reveal/' . $filename,
                ];
            })
            ->filter(function ($img) use ($q) {
                if ($q === '') return true;
                $haystack = strtolower($img['name'] . ' ' . implode(' ', $img['tags']));
                return str_contains($haystack, $q);
            })
            ->values();

        return response()->json($files);
    }

    public function webSearchRevealImages(Request $request)
    {
        auth()->user()->events()->firstOrFail();

        $q = trim((string) $request->query('q', ''));
        if ($q === '') return response()->json([]);

        try {
            $ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36';

            // Paso 1: Obtener token vqd desde la búsqueda inicial
            $bootstrap = Http::withHeaders(['User-Agent' => $ua])
                ->timeout(10)
                ->get('https://duckduckgo.com/', ['q' => $q]);

            if (!$bootstrap->successful()) {
                return response()->json(['error' => 'DuckDuckGo no respondió'], 502);
            }

            if (!preg_match('/vqd=[\'"]?([\d-]+)[\'"]?/', $bootstrap->body(), $m)) {
                return response()->json(['error' => 'No se pudo obtener el token vqd'], 502);
            }
            $vqd = $m[1];

            // Paso 2: Buscar imágenes con el token
            $response = Http::withHeaders([
                'User-Agent' => $ua,
                'Referer'    => 'https://duckduckgo.com/',
                'Accept'     => 'application/json, text/javascript, */*; q=0.01',
            ])->timeout(10)->get('https://duckduckgo.com/i.js', [
                'l'     => 'us-en',
                'o'     => 'json',
                'q'     => $q,
                'vqd'   => $vqd,
                'f'     => ',,,,,',
                'p'     => '1',
                'v7exp' => 'a',
            ]);

            if (!$response->successful()) {
                return response()->json(['error' => 'Búsqueda de imágenes falló'], 502);
            }

            $images = collect($response->json('results') ?? [])
                ->take(24)
                ->map(fn($it) => [
                    'title'     => $it['title'] ?? '',
                    'thumbnail' => $it['thumbnail'] ?? $it['image'] ?? null,
                    'url'       => $it['image'] ?? null,
                ])
                ->filter(fn($it) => !empty($it['url']) && !empty($it['thumbnail']))
                ->values();

            return response()->json($images);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error de red: ' . $e->getMessage()], 502);
        }
    }

    public function importRevealImage(Request $request)
    {
        $data = $request->validate([
            'slot'  => 'required|in:1,2',
            'url'   => 'required|url|max:2000',
            'title' => 'nullable|string|max:200',
        ]);

        $event = auth()->user()->events()->firstOrFail();

        try {
            $response = Http::timeout(15)->get($data['url']);
            if (!$response->successful()) abort(422, 'No se pudo descargar la imagen');

            $contentType = strtolower($response->header('Content-Type') ?? '');
            $ext = match (true) {
                str_contains($contentType, 'png')  => 'png',
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'gif')  => 'gif',
                str_contains($contentType, 'jpeg'), str_contains($contentType, 'jpg') => 'jpg',
                default => 'jpg',
            };

            if (strlen($response->body()) > 5 * 1024 * 1024) abort(422, 'Imagen demasiado grande');

            $slug = \Illuminate\Support\Str::slug($data['title'] ?: 'imagen', '-');
            if ($slug === '') $slug = 'imagen';
            $filename = $slug . '_web_' . substr(md5($data['url']), 0, 8) . '.' . $ext;
            $relPath = 'images/reveal/' . $filename;
            file_put_contents(public_path($relPath), $response->body());

            $event->update(['reveal_image_' . $data['slot'] => $relPath]);

            return back()->with('saved', '🎨 Imagen importada del catálogo web');
        } catch (\Exception $e) {
            abort(422, 'Error al importar: ' . $e->getMessage());
        }
    }

    public function setRevealImage(Request $request)
    {
        $data = $request->validate([
            'slot' => 'required|in:1,2',
            'path' => 'required|string|max:200',
        ]);

        if (!str_starts_with($data['path'], 'images/reveal/')
            || !file_exists(public_path($data['path']))) {
            abort(422, 'Imagen no válida');
        }

        $event = auth()->user()->events()->firstOrFail();
        $event->update(['reveal_image_' . $data['slot'] => $data['path']]);

        return back()->with('saved', '🎨 Imagen de revelación actualizada');
    }

    public function removeRevealImage(Request $request)
    {
        $request->validate(['slot' => 'required|in:1,2']);
        $event = auth()->user()->events()->firstOrFail();
        $column = 'reveal_image_' . (int) $request->input('slot');
        $path = $event->{$column};

        if ($path && str_starts_with($path, 'storage/')) {
            $relative = substr($path, 8);
            Storage::disk('public')->delete($relative);
        }

        $event->update([$column => null]);
        return back()->with('saved', '🗑️ Imagen quitada');
    }

    public function uploadRevealImage(Request $request)
    {
        $request->validate([
            'slot'  => 'required|in:1,2',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
        ]);

        $event = auth()->user()->events()->firstOrFail();
        $slot = (int) $request->input('slot');
        $column = "reveal_image_{$slot}";

        $oldPath = $event->{$column};
        if ($oldPath && str_starts_with($oldPath, 'storage/')) {
            $relative = substr($oldPath, 8);
            Storage::disk('public')->delete($relative);
        }

        $path = $request->file('image')->store("events/{$event->id}", 'public');
        $event->update([$column => 'storage/' . $path]);

        return back()->with('saved', '🎨 Imagen de revelación subida correctamente');
    }

    public function deleteGuest(Guest $guest)
    {
        $event = auth()->user()->events()->firstOrFail();
        if ($guest->event_id === $event->id) {
            $guest->delete();
            return back()->with('saved', '🗑️ Invitado eliminado');
        }
        abort(403);
    }
}
