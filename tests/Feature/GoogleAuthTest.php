<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_redirects_unauthenticated_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Panel de Control');
    }

    public function test_bypass_login_works_in_local_environment(): void
    {
        // Forzar entorno local y simular que GOOGLE_CLIENT_ID está vacío
        $this->app['env'] = 'local';
        config(['services.google.client_id' => '']);

        $response = $this->get('/auth/bypass');

        $response->assertRedirect('/admin');
        $this->assertAuthenticated();
    }

    public function test_logout_redirects_to_login(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
