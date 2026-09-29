<?php

namespace Tests\Feature;

use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_de_login_se_muestra(): void
    {
        $this->get('/login')->assertOk()->assertSee('Iniciar sesión');
    }

    public function test_usuario_puede_iniciar_sesion_y_va_al_historial(): void
    {
        $user = User::factory()->create(['password' => 'secreto123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'secreto123'])
            ->assertRedirect(route('historial.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_credenciales_incorrectas_no_inician_sesion(): void
    {
        $user = User::factory()->create(['password' => 'secreto123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'incorrecta'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_exige_email_y_password(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_se_bloquea_tras_demasiados_intentos(): void
    {
        $user = User::factory()->create(['password' => 'secreto123']);

        foreach (range(1, 5) as $i) {
            $this->post('/login', ['email' => $user->email, 'password' => 'mal']);
        }

        // Aun con la contraseña correcta, queda bloqueado.
        $this->post('/login', ['email' => $user->email, 'password' => 'secreto123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_cierra_la_sesion(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/logout')
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_invitado_es_redirigido_al_login_desde_el_historial(): void
    {
        $this->get('/historial')->assertRedirect(route('login'));
    }

    public function test_usuario_logueado_no_ve_login_ni_registro(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/login')->assertRedirect(route('historial.index'));
        $this->get('/register')->assertRedirect(route('historial.index'));
    }

    public function test_registro_crea_usuario_encola_bienvenida_y_redirige_al_login(): void
    {
        Queue::fake();

        $this->post('/register', [
            'name' => 'Juan Pérez',
            'email' => 'juan@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', ['email' => 'juan@gmail.com']);
        Queue::assertPushed(SendWelcomeEmailJob::class);
    }

    public function test_navbar_cambia_segun_el_estado_de_sesion(): void
    {
        $this->get('/')->assertSee('Iniciar sesión')->assertSee('Registrarse')->assertDontSee('Historial');

        $this->actingAs(User::factory()->create(['name' => 'Ana Gómez']))
            ->get('/')
            ->assertSee('Historial')
            ->assertSee('Ana Gómez')
            ->assertSee('Salir');
    }
}
