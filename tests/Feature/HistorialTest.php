<?php

namespace Tests\Feature;

use App\Models\SentMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class HistorialTest extends TestCase
{
    use RefreshDatabase;

    private function correo(User $user, array $extra = []): SentMail
    {
        return SentMail::create(array_merge([
            'user_id' => $user->id,
            'destinatario' => 'alumno@example.com',
            'nombre' => 'Alumno',
            'asunto' => 'Asunto de prueba',
            'mensaje' => 'Hola',
            'estado' => 'enviado',
        ], $extra));
    }

    public function test_historial_solo_muestra_los_correos_del_usuario(): void
    {
        $yo = User::factory()->create();
        $otro = User::factory()->create();

        $this->correo($yo, ['asunto' => 'Mio']);
        $this->correo($otro, ['asunto' => 'Ajeno']);

        $this->actingAs($yo)->get('/historial')
            ->assertOk()
            ->assertSee('Mio')
            ->assertDontSee('Ajeno');
    }

    public function test_historial_vacio_muestra_mensaje(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/historial')
            ->assertSee('Todavía no enviaste ningún correo');
    }

    public function test_historial_filtra_por_busqueda_y_estado(): void
    {
        $yo = User::factory()->create();
        $this->correo($yo, ['asunto' => 'Factura marzo']);
        $this->correo($yo, ['asunto' => 'Reunión lunes', 'estado' => 'fallido']);

        $this->actingAs($yo)->get('/historial?q=Factura')
            ->assertSee('Factura marzo')->assertDontSee('Reunión lunes');

        $this->actingAs($yo)->get('/historial?estado=fallido')
            ->assertSee('Reunión lunes')->assertDontSee('Factura marzo');
    }

    public function test_detalle_de_un_correo_propio(): void
    {
        $yo = User::factory()->create();
        $correo = $this->correo($yo, ['cc' => 'copia@example.com']);

        $this->actingAs($yo)->get(route('historial.show', $correo))
            ->assertOk()->assertSee('Asunto de prueba')->assertSee('copia@example.com');
    }

    public function test_no_se_puede_ver_ni_borrar_el_correo_de_otro(): void
    {
        $yo = User::factory()->create();
        $correo = $this->correo(User::factory()->create());

        $this->actingAs($yo)->get(route('historial.show', $correo))->assertNotFound();
        $this->actingAs($yo)->delete(route('historial.destroy', $correo))->assertNotFound();
        $this->assertDatabaseHas('sent_mails', ['id' => $correo->id]);
    }

    public function test_se_puede_eliminar_un_registro_propio(): void
    {
        $yo = User::factory()->create();
        $correo = $this->correo($yo);

        $this->actingAs($yo)->delete(route('historial.destroy', $correo))
            ->assertRedirect(route('historial.index'));

        $this->assertDatabaseMissing('sent_mails', ['id' => $correo->id]);
    }

    public function test_envio_por_post_con_sesion_queda_en_el_historial(): void
    {
        Mail::fake();
        $yo = User::factory()->create();

        $this->actingAs($yo)->post('/mail/enviar', [
            'destinatario' => 'alumno@example.com',
            'nombre' => 'Juan',
            'asunto' => 'Con sesión',
            'mensaje' => 'Hola',
        ]);

        $this->assertDatabaseHas('sent_mails', [
            'user_id' => $yo->id,
            'asunto' => 'Con sesión',
            'estado' => 'enviado',
        ]);
    }

    public function test_envio_de_invitado_no_tiene_usuario(): void
    {
        Mail::fake();

        $this->post('/mail/enviar', [
            'destinatario' => 'alumno@example.com',
            'nombre' => 'Juan',
            'asunto' => 'Invitado',
            'mensaje' => 'Hola',
        ]);

        $this->assertDatabaseHas('sent_mails', ['asunto' => 'Invitado', 'user_id' => null]);
    }

    public function test_envio_fallido_se_registra_como_fallido(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP caído'));
        $yo = User::factory()->create();

        $this->actingAs($yo)->post('/mail/enviar', [
            'destinatario' => 'alumno@example.com',
            'nombre' => 'Juan',
            'asunto' => 'Va a fallar',
            'mensaje' => 'Hola',
        ])->assertSessionHas('error');

        $this->assertDatabaseHas('sent_mails', [
            'asunto' => 'Va a fallar',
            'estado' => 'fallido',
            'error' => 'SMTP caído',
        ]);
    }
}
