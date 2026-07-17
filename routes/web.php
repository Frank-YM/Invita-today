<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InvitationController::class, 'show'])->name('invitation');
Route::get('/e/{slug}', [InvitationController::class, 'show'])->name('invitation.public');
Route::post('/rsvp', [InvitationController::class, 'rsvp'])->name('rsvp');

// Galería pública (invitados suben sin login) — máximo 50 subidas por hora por IP
Route::post('/e/{slug}/gallery', [InvitationController::class, 'uploadGuestPhoto'])
    ->middleware('throttle:50,60')
    ->name('gallery.upload');

// Rutas de autenticación
Route::get('/login', [GoogleAuthController::class, 'showLogin'])->name('login');
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');

// Rutas de Inteligencia Artificial públicas
Route::post('/ai/chat', [AIController::class, 'chat'])->name('ai.chat');
Route::get('/auth/bypass', [GoogleAuthController::class, 'bypass'])->name('auth.bypass');

// Rutas protegidas de administración
Route::middleware('auth')->group(function () {
    Route::get('/mis-eventos', [InvitationController::class, 'eventsIndex'])->name('events.index');
    Route::post('/mis-eventos', [InvitationController::class, 'eventsStore'])->name('events.store');
    Route::get('/mis-eventos/{event}/seleccionar', [InvitationController::class, 'eventsSelect'])->name('events.select');
    Route::delete('/mis-eventos/{event}', [InvitationController::class, 'eventsDestroy'])->name('events.destroy');

    Route::get('/admin', [InvitationController::class, 'admin'])->name('admin');
    Route::post('/admin/event', [InvitationController::class, 'updateEvent'])->name('event.update');
    Route::post('/admin/event/publish', [InvitationController::class, 'togglePublish'])->name('event.publish');
    Route::post('/admin/event/photo', [InvitationController::class, 'uploadPhoto'])->name('event.photo.upload');
    Route::delete('/admin/event/photo', [InvitationController::class, 'deletePhoto'])->name('event.photo.delete');
    Route::post('/admin/event/music', [InvitationController::class, 'uploadMusic'])->name('event.music.upload');
    Route::delete('/admin/event/music', [InvitationController::class, 'deleteMusic'])->name('event.music.delete');
    Route::get('/admin/reveal-images/search', [InvitationController::class, 'searchRevealImages'])->name('reveal.search');
    Route::get('/admin/reveal-images/web-search', [InvitationController::class, 'webSearchRevealImages'])->name('reveal.search.web');
    Route::post('/admin/event/reveal-image/import', [InvitationController::class, 'importRevealImage'])->name('event.reveal.import');
    Route::post('/admin/event/reveal-image', [InvitationController::class, 'setRevealImage'])->name('event.reveal.set');
    Route::post('/admin/event/reveal-image/upload', [InvitationController::class, 'uploadRevealImage'])->name('event.reveal.upload');
    Route::delete('/admin/event/reveal-image', [InvitationController::class, 'removeRevealImage'])->name('event.reveal.remove');
    Route::delete('/admin/guests/{guest}', [InvitationController::class, 'deleteGuest'])->name('guest.delete');
    Route::delete('/admin/gallery/{photo}', [InvitationController::class, 'deleteGuestPhoto'])->name('gallery.delete');
    
    // IA para administradores
    Route::post('/admin/ai/generate', [AIController::class, 'generate'])->name('ai.generate');

    // Super admin
    Route::get('/super-admin', [SuperAdminController::class, 'index'])
        ->middleware('super_admin')
        ->name('super_admin');
});
