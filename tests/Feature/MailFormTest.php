<?php

namespace Tests\Feature;

use App\Livewire\MailForm;
use App\Mail\TestBrevoMail;
use App\Models\SentMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class MailFormTest extends TestCase
{
    use RefreshDatabase;

    private function completo(): \Livewire\Features\SupportTesting\Testable
    {
        return Livewire::test(MailForm::class)
            ->set('destinatario', 'uno@example.com, dos@example.com')
            ->set('cc', 'copia@example.com')
            ->set('cco', 'oculta@example.com')
            ->set('nombre', 'Juan Pérez')
            ->set('asunto', 'Asunto')
            ->set('mensaje', 'Cuerpo del mensaje');
    }

    public function test_pagina_mail_incluye_el_componente(): void
    {
        $this->get('/mail')
            ->assertOk()
            ->assertSeeLivewire(MailForm::class)
            ->assertSee('CC')
            ->assertSee('CCO');
    }

    public function test_campos_obligatorios(): void
    {
        Livewire::test(MailForm::class)
            ->call('revisar')
            ->assertHasErrors(['destinatario', 'nombre', 'asunto', 'mensaje'])
            ->assertSet('paso', 'editar');
    }

    public function test_valida_cada_correo_de_la_lista(): void
    {
        Livewire::test(MailForm::class)
            ->set('destinatario', 'bien@example.com, mal-correo')
            ->set('cc', 'otro-mal')
            ->call('revisar')
            ->assertHasErrors(['destinatario', 'cc']);
    }

    public function test_telefono_minimo_8_caracteres(): void
    {
        $this->completo()->set('telefono', '123')->call('revisar')->assertHasErrors('telefono');
    }

    public function test_pasa_a_la_pantalla_de_confirmacion_sin_enviar(): void
    {
        Mail::fake();

        $this->completo()
            ->call('revisar')
            ->assertSet('paso', 'confirmar')
            ->assertSee('Revisá el correo antes de enviarlo')
            ->assertSee('uno@example.com')
            ->assertSee('oculta@example.com')
            ->assertSee('Confirmar y enviar');

        Mail::assertNothingSent();
    }

    public function test_volver_regresa_a_editar_conservando_los_datos(): void
    {
        $this->completo()->call('revisar')->call('volver')
            ->assertSet('paso', 'editar')
            ->assertSet('asunto', 'Asunto');
    }

    public function test_confirmar_envia_a_todos_los_destinatarios_con_cc_y_cco(): void
    {
        Mail::fake();

        $this->completo()->call('revisar')->call('enviar')
            ->assertSet('exito', 'El correo fue enviado correctamente.')
            ->assertSet('asunto', '');

        Mail::assertSent(TestBrevoMail::class, function ($mail) {
            return $mail->hasTo('uno@example.com')
                && $mail->hasTo('dos@example.com')
                && $mail->hasCc('copia@example.com')
                && $mail->hasBcc('oculta@example.com')
                && $mail->nombre === 'Juan Pérez';
        });
    }

    public function test_envio_con_adjunto_y_registro_en_historial(): void
    {
        Mail::fake();
        $yo = User::factory()->create();
        $this->actingAs($yo);

        $this->completo()
            ->set('archivos', [UploadedFile::fake()->create('informe.pdf', 100, 'application/pdf')])
            ->call('revisar')
            ->assertSee('informe.pdf')
            ->call('enviar');

        Mail::assertSent(TestBrevoMail::class, fn ($mail) => count($mail->adjuntos) === 1
            && $mail->adjuntos[0]['name'] === 'informe.pdf');

        $registro = SentMail::firstOrFail();
        $this->assertSame($yo->id, $registro->user_id);
        $this->assertSame('informe.pdf', $registro->adjuntos[0]['name']);
        $this->assertSame('uno@example.com, dos@example.com', $registro->destinatario);
    }

    public function test_rechaza_adjuntos_muy_pesados_o_no_permitidos(): void
    {
        $this->completo()
            ->set('archivos', [UploadedFile::fake()->create('grande.pdf', 3000, 'application/pdf')])
            ->call('revisar')
            ->assertHasErrors('archivos.0');

        $this->completo()
            ->set('archivos', [UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload')])
            ->call('revisar')
            ->assertHasErrors('archivos.0');
    }

    public function test_maximo_tres_adjuntos(): void
    {
        $this->completo()
            ->set('archivos', array_map(fn ($i) => UploadedFile::fake()->create("a{$i}.pdf", 10, 'application/pdf'), range(1, 4)))
            ->call('revisar')
            ->assertHasErrors('archivos');
    }

    public function test_fallo_de_smtp_muestra_error_y_vuelve_a_editar(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP caído'));

        $this->completo()->call('revisar')->call('enviar')
            ->assertSet('paso', 'editar')
            ->assertSet('fallo', 'No se pudo enviar el correo. Verifique la configuración SMTP.')
            ->assertSet('asunto', 'Asunto');
    }

    public function test_reenviar_precarga_los_datos_del_historial(): void
    {
        $yo = User::factory()->create();
        $correo = SentMail::create([
            'user_id' => $yo->id, 'destinatario' => 'a@example.com', 'nombre' => 'A',
            'asunto' => 'Viejo', 'mensaje' => 'Texto', 'estado' => 'enviado',
        ]);

        $this->actingAs($yo);

        Livewire::test(MailForm::class, ['reenviar' => $correo->id])
            ->assertSet('asunto', 'Viejo')
            ->assertSet('destinatario', 'a@example.com');
    }

    public function test_reenviar_ignora_correos_de_otro_usuario(): void
    {
        $correo = SentMail::create([
            'user_id' => User::factory()->create()->id, 'destinatario' => 'a@example.com', 'nombre' => 'A',
            'asunto' => 'Privado', 'mensaje' => 'Texto', 'estado' => 'enviado',
        ]);

        $this->actingAs(User::factory()->create());

        Livewire::test(MailForm::class, ['reenviar' => $correo->id])->assertSet('asunto', '');
    }
}
