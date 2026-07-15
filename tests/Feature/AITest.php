<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AITest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_generate_requires_authentication(): void
    {
        $response = $this->postJson('/admin/ai/generate', [
            'field' => 'subtitle',
            'event_type' => 'babyshower'
        ]);

        $response->assertStatus(401);
    }

    public function test_ai_generate_returns_mock_when_no_api_key(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Forzar clave vacía de Gemini
        config(['services.gemini.key' => '']); 

        $response = $this->postJson('/admin/ai/generate', [
            'field' => 'subtitle',
            'event_type' => 'babyshower'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['text']);
        $this->assertNotEmpty($response->json('text'));
    }

    public function test_ai_chat_works_publicly_and_returns_valid_reply(): void
    {
        $event = Event::current();
        $event->update([
            'place' => 'Casa de los Abuelos, Calle Sol 456',
            'date' => '2026-08-15 16:00:00'
        ]);

        $response = $this->postJson('/ai/chat', [
            'message' => '¿Dónde es el lugar de la fiesta?'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply']);
        $response->assertJsonFragment([
            'reply' => "El evento se llevará a cabo en: Casa de los Abuelos, Calle Sol 456. ¡Te esperamos!"
        ]);
    }
}
