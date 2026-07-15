<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'     => User::count(),
            'total_events'    => Event::count(),
            'published'       => Event::where('is_published', true)->count(),
            'total_guests'    => Guest::count(),
            'attending'       => Guest::where('attending', true)->count(),
            'active_last_7d'  => User::whereNotNull('last_login_at')
                                     ->where('last_login_at', '>=', now()->subDays(7))
                                     ->count(),
        ];

        $users = User::withCount('events')
            ->orderByDesc('last_login_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($u) => [
                'id'            => $u->id,
                'name'          => $u->name,
                'email'         => $u->email,
                'is_super_admin'=> $u->is_super_admin,
                'events_count'  => $u->events_count,
                'created_at'    => $u->created_at,
                'last_login_at' => $u->last_login_at,
            ]);

        $templates = Event::select('template')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('template')
            ->orderByDesc('total')
            ->get();

        $eventTypes = Event::select('event_type')
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('event_type')
            ->groupBy('event_type')
            ->orderByDesc('total')
            ->get();

        $recentEvents = Event::with('user:id,name,email')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($e) => [
                'title'        => $e->title,
                'slug'         => $e->slug,
                'template'     => $e->template,
                'event_type'   => $e->event_type,
                'is_published' => $e->is_published,
                'user_name'    => optional($e->user)->name,
                'user_email'   => optional($e->user)->email,
                'created_at'   => $e->created_at,
            ]);

        return view('super-admin', compact('stats', 'users', 'templates', 'eventTypes', 'recentEvents'));
    }
}
