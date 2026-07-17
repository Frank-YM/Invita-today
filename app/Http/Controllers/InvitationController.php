<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Models\GuestPhoto;
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
        $galleryPhotos = $event->show_gallery ? $event->guestPhotos()->take(60)->get() : collect();

        return view('invitation', compact('event', 'confirmed', 'messages', 'isPreview', 'isOwner', 'galleryPhotos'));
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

        if ($user->events()->count() === 0) {
            return redirect()->route('events.index');
        }

        $event = $this->currentEvent();
        if (!$event) {
            return redirect()->route('events.index');
        }

        if (!$event->slug) {
            $event->update([
                'slug' => $this->generateUniqueSlug($event->title, $event->event_type, $event->id),
            ]);
        }

        $guests = $event->guests()->latest()->get();
        $totalPeople = $event->guests()->where('attending', true)->sum('companions')
                     + $event->guests()->where('attending', true)->count();
        $galleryPhotos = $event->guestPhotos()->get();
        $userEvents = $user->events()->orderBy('date')->get();

        return view('admin', compact('event', 'guests', 'totalPeople', 'galleryPhotos', 'userEvents'));
    }

    /** Devuelve el evento activo del usuario (guardado en session) o el primero */
    private function currentEvent(): ?Event
    {
        $user = auth()->user();
        $activeId = session('active_event_id');

        if ($activeId) {
            $event = $user->events()->where('id', $activeId)->first();
            if ($event) return $event;
        }

        $event = $user->events()->first();
        if ($event) session(['active_event_id' => $event->id]);
        return $event;
    }

    /** Listado "Mis eventos" */
    public function eventsIndex()
    {
        $user = auth()->user();
        $events = $user->events()->withCount('guests')->orderByDesc('created_at')->get();
        $activeId = session('active_event_id');

        return view('events-index', compact('events', 'activeId'));
    }

    /** Crear evento nuevo desde el listado */
    public function eventsStore(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:120',
            'event_type' => 'required|string|max:30|in:babyshower,cumple,bautizo,revelacion,bienvenida,comunion,boda,quinceanero,graduacion,aniversario,despedida,general',
        ]);

        $user = auth()->user();
        $event = $user->events()->create([
            'title'      => $data['title'],
            'event_type' => $data['event_type'],
            'slug'       => $this->generateUniqueSlug($data['title'], $data['event_type']),
        ]);

        session(['active_event_id' => $event->id]);
        return redirect()->route('admin')->with('saved', '🎉 Evento creado. Ya podés configurarlo.');
    }

    /** Marcar un evento como activo y saltar al admin */
    public function eventsSelect(Event $event)
    {
        if ($event->user_id !== auth()->id()) abort(403);
        session(['active_event_id' => $event->id]);
        return redirect()->route('admin');
    }

    /** Eliminar un evento */
    public function eventsDestroy(Event $event)
    {
        if ($event->user_id !== auth()->id()) abort(403);

        foreach (['photo_1', 'photo_2'] as $col) {
            if ($event->{$col}) {
                Storage::disk('public')->delete($event->{$col});
            }
        }
        foreach ($event->guestPhotos as $p) {
            if (str_starts_with($p->path, 'storage/')) {
                Storage::disk('public')->delete(substr($p->path, 8));
            }
        }

        $event->delete();

        if (session('active_event_id') === $event->id) {
            session()->forget('active_event_id');
        }

        return redirect()->route('events.index')->with('saved', '🗑️ Evento eliminado');
    }

    private function generateUniqueSlug(string $title, ?string $eventType = null, ?int $ignoreId = null): string
    {
        $titleSlug = Str::slug($title) ?: 'evento';
        $typeSlug  = $eventType ? Str::slug($eventType) : null;

        $titleCompact = str_replace('-', '', $titleSlug);
        $typeCompact  = $typeSlug ? str_replace('-', '', $typeSlug) : null;

        $base = ($typeSlug && !str_starts_with($titleCompact, $typeCompact))
            ? $typeSlug . '-' . $titleSlug
            : $titleSlug;

        $slug = $base;
        while (Event::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . Str::lower(Str::random(4));
        }
        return $slug;
    }

    private function detectEventTypeFromTitle(string $title): ?string
    {
        $t = mb_strtolower($title);
        // Orden importa: los más específicos primero.
        if (str_contains($t, 'baby shower') || str_contains($t, 'babyshower')) return 'babyshower';
        if (str_contains($t, 'revelación') || str_contains($t, 'revelacion')
            || str_contains($t, 'género') || str_contains($t, 'genero')) return 'revelacion';
        if (str_contains($t, 'primera comunión') || str_contains($t, 'primera comunion')
            || str_contains($t, 'comunión') || str_contains($t, 'comunion')) return 'comunion';
        if (str_contains($t, 'bautizo') || str_contains($t, 'bautismo')) return 'bautizo';
        if (str_contains($t, 'bienvenida')) return 'bienvenida';
        if (str_contains($t, 'despedida de solter') || str_contains($t, 'bachelor')
            || str_contains($t, 'bachelorette') || str_contains($t, 'despedida')) return 'despedida';
        if (str_contains($t, 'quince') || str_contains($t, 'quinceañera')
            || str_contains($t, 'quinceanera') || str_contains($t, 'xv años')
            || str_contains($t, 'xv anos') || str_contains($t, 'mis 15')) return 'quinceanero';
        if (str_contains($t, 'graduación') || str_contains($t, 'graduacion')
            || str_contains($t, 'graduación') || str_contains($t, 'grado')
            || str_contains($t, 'promoción') || str_contains($t, 'promocion')) return 'graduacion';
        if (str_contains($t, 'aniversario') || str_contains($t, 'bodas de plata')
            || str_contains($t, 'bodas de oro')) return 'aniversario';
        if (str_contains($t, 'boda') || str_contains($t, 'matrimonio')
            || str_contains($t, 'casamiento')) return 'boda';
        if (str_contains($t, 'cumpleaños') || str_contains($t, 'cumpleanos')
            || str_contains($t, 'cumple')) return 'cumple';
        return null;
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
        
        $event = $this->currentEvent();
        abort_if(!$event, 404);
        $oldTitle = $event->title;
        $oldType  = $event->event_type;

        $data['is_published'] = $request->boolean('is_published');
        foreach (['show_countdown', 'show_map', 'show_confirmed_count', 'show_messages', 'show_gallery'] as $flag) {
            $data[$flag] = $request->has($flag) ? $request->boolean($flag) : $event->{$flag};
        }

        $typeAutoSynced = false;
        if ($oldTitle !== $data['title']) {
            $requestedType = $data['event_type'] ?? $oldType;
            $detected = $this->detectEventTypeFromTitle($data['title']);
            if ($detected && $requestedType === $oldType && $detected !== $oldType) {
                $data['event_type'] = $detected;
                $typeAutoSynced = true;
            }
        }

        $event->update($data);

        if ($oldTitle !== $event->title || $oldType !== $event->event_type) {
            $event->update([
                'slug' => $this->generateUniqueSlug($event->title, $event->event_type, $event->id),
            ]);
        }

        $msg = '✅ Evento actualizado correctamente';
        if ($typeAutoSynced) {
            $labels = [
                'babyshower' => 'Baby Shower', 'cumple' => 'Cumpleaños',
                'bautizo' => 'Bautizo', 'revelacion' => 'Revelación',
                'bienvenida' => 'Bienvenida', 'comunion' => 'Comunión',
                'boda' => 'Boda', 'quinceanero' => 'Quinceañero',
                'graduacion' => 'Graduación', 'aniversario' => 'Aniversario',
                'despedida' => 'Despedida', 'general' => 'Evento general',
            ];
            $newLabel = $labels[$event->event_type] ?? $event->event_type;
            $msg .= ' · Ajustamos el tipo de evento a "' . $newLabel . '" según el título.';
        }
        return redirect()->route('admin')->with('saved', $msg);
    }

    public function togglePublish(Request $request)
    {
        $event = $this->currentEvent();
        abort_if(!$event, 404);
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

        $event = $this->currentEvent();
        abort_if(!$event, 404);
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

        $event = $this->currentEvent();
        abort_if(!$event, 404);
        $column = 'photo_' . (int) $request->input('slot');

        if ($event->{$column}) {
            Storage::disk('public')->delete($event->{$column});
            $event->update([$column => null]);
        }

        return back()->with('saved', '🗑️ Foto eliminada');
    }

    public function uploadMusic(Request $request)
    {
        $request->validate([
            'music' => 'required|file|mimes:mp3,m4a,aac,ogg,wav|max:10240',
        ]);

        $event = $this->currentEvent();
        abort_if(!$event, 404);

        if ($event->music_path) {
            Storage::disk('public')->delete($event->music_path);
        }

        $path = $request->file('music')->store("events/{$event->id}/music", 'public');
        $event->update(['music_path' => $path]);

        return back()->with('saved', '🎵 Música subida correctamente');
    }

    public function deleteMusic()
    {
        $event = $this->currentEvent();
        abort_if(!$event, 404);

        if ($event->music_path) {
            Storage::disk('public')->delete($event->music_path);
            $event->update(['music_path' => null]);
        }

        return back()->with('saved', '🗑️ Música eliminada');
    }

    public function searchRevealImages(Request $request)
    {
        abort_if(!$this->currentEvent(), 404);

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
        abort_if(!$this->currentEvent(), 404);

        $q = trim((string) $request->query('q', ''));
        if ($q === '') return response()->json([]);

        $apiKey = env('PIXABAY_API_KEY');
        if (empty($apiKey)) {
            return response()->json(['error' => 'Buscador de imágenes no configurado'], 500);
        }

        try {
            $response = Http::timeout(15)->get('https://pixabay.com/api/', [
                'key'        => $apiKey,
                'q'          => $q,
                'image_type' => 'all',
                'safesearch' => 'true',
                'per_page'   => 24,
                'lang'       => 'es',
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Pixabay respondió ' . $response->status(),
                ], 502);
            }

            $images = collect($response->json('hits') ?? [])
                ->map(fn($it) => [
                    'title'     => $it['tags'] ?? '',
                    'thumbnail' => $it['previewURL'] ?? $it['webformatURL'] ?? null,
                    'url'       => $it['largeImageURL'] ?? $it['webformatURL'] ?? null,
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

        $event = $this->currentEvent();
        abort_if(!$event, 404);

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

        $event = $this->currentEvent();
        abort_if(!$event, 404);
        $event->update(['reveal_image_' . $data['slot'] => $data['path']]);

        return back()->with('saved', '🎨 Imagen de revelación actualizada');
    }

    public function removeRevealImage(Request $request)
    {
        $request->validate(['slot' => 'required|in:1,2']);
        $event = $this->currentEvent();
        abort_if(!$event, 404);
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

        $event = $this->currentEvent();
        abort_if(!$event, 404);
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
        $event = $this->currentEvent();
        abort_if(!$event, 404);
        if ($guest->event_id === $event->id) {
            $guest->delete();
            return back()->with('saved', '🗑️ Invitado eliminado');
        }
        abort(403);
    }

    /** Subida pública de foto por parte de un invitado */
    public function uploadGuestPhoto(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        if (!$event->is_published || !$event->show_gallery) {
            abort(404);
        }

        $data = $request->validate([
            'guest_name' => 'required|string|max:60',
            'photo'      => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $stored = $request->file('photo')->store("gallery/{$event->id}", 'public');
        $absolute = Storage::disk('public')->path($stored);
        $this->resizePhoto($absolute, 1200);

        GuestPhoto::create([
            'event_id'   => $event->id,
            'guest_name' => trim($data['guest_name']),
            'path'       => 'storage/' . $stored,
        ]);

        return back()->with('gallery_success', '📸 ¡Tu foto se sumó a la galería!');
    }

    public function deleteGuestPhoto(GuestPhoto $photo)
    {
        // Debe pertenecer a algún evento del usuario logueado (no solo al activo).
        $photo->load('event');
        abort_if(!$photo->event || $photo->event->user_id !== auth()->id(), 403);

        if (str_starts_with($photo->path, 'storage/')) {
            Storage::disk('public')->delete(substr($photo->path, 8));
        }
        $photo->delete();

        return back()->with('saved', '🗑️ Foto eliminada');
    }

    /** Reduce dimensiones máximas y recomprime la foto guardada */
    private function resizePhoto(string $absolutePath, int $maxSide): void
    {
        if (!extension_loaded('gd') || !is_file($absolutePath)) return;

        $info = @getimagesize($absolutePath);
        if (!$info) return;
        [$w, $h, $type] = $info;
        if ($w <= $maxSide && $h <= $maxSide) return;

        $ratio  = min($maxSide / $w, $maxSide / $h);
        $newW   = (int) round($w * $ratio);
        $newH   = (int) round($h * $ratio);

        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($absolutePath),
            IMAGETYPE_PNG  => @imagecreatefrompng($absolutePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($absolutePath) : null,
            default        => null,
        };
        if (!$src) return;

        $dst = imagecreatetruecolor($newW, $newH);
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($dst, $absolutePath, 75),
            IMAGETYPE_PNG  => imagepng($dst, $absolutePath, 7),
            IMAGETYPE_WEBP => function_exists('imagewebp') ? imagewebp($dst, $absolutePath, 75) : null,
            default        => null,
        };

        imagedestroy($src);
        imagedestroy($dst);
    }
}
