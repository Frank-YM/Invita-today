<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitation_page_shows_coming_soon_when_not_published_and_no_preview(): void
    {
        // El evento único inicia como no publicado por defecto
        $event = Event::current();
        $event->update(['is_published' => false]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Próximamente');
        $response->assertDontSee('Confirmar asistencia');
    }

    public function test_invitation_page_shows_full_invitation_when_published(): void
    {
        $event = Event::current();
        $event->update([
            'is_published' => true,
            'title' => 'Mi Baby Shower Especial',
            'rsvp_button_text' => 'Confirmar Ahora'
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Mi Baby Shower Especial');
        $response->assertSee('Confirmar Ahora');
        $response->assertDontSee('Próximamente');
    }

    public function test_invitation_page_shows_preview_when_not_published_but_preview_param_present(): void
    {
        $event = Event::current();
        $event->update([
            'is_published' => false,
            'title' => 'Vista Previa de Borrador',
        ]);

        $response = $this->get('/?preview=1');

        $response->assertStatus(200);
        $response->assertSee('Vista Previa de Borrador');
        $response->assertSee('Vista Previa (Modo Borrador)');
    }

    public function test_invitation_renders_theme_character_image(): void
    {
        $event = Event::current();
        $event->update([
            'is_published' => true,
            'theme_character' => 'goku_nino',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('images/themes/goku_nino.png');
    }
}
