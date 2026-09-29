<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestBrevoMail;

class MailTest extends TestCase
{
    use RefreshDatabase;

    // Test N.° 1 — Comprobar que el formulario funciona
    public function test_formulario_mail_se_muestra_correctamente(): void
    {
        $response = $this->get('/mail');

        $response->assertStatus(200);

        $response->assertSee('Envío de correo');
        $response->assertSee('Correo destinatario');
        $response->assertSee('Asunto');
        $response->assertSee('Mensaje');
        $response->assertSee('Enviar correo');
    }

    // Test N.° 2 — Campos obligatorios
    public function test_campos_del_formulario_son_obligatorios(): void
    {
        $response = $this->post('/mail/enviar', []);

        $response->assertSessionHasErrors([
            'destinatario',
            'nombre',
            'asunto',
            'mensaje'
        ]);
    }

    // Test N.° 3 — Email inválido
    public function test_destinatario_debe_ser_un_email_valido(): void
    {
        $response = $this->post('/mail/enviar', [
            'destinatario' => 'correo-invalido',
            'nombre' => 'Juan Pérez',
            'asunto' => 'Prueba',
            'mensaje' => 'Mensaje de prueba'
        ]);

        $response->assertSessionHasErrors('destinatario');
    }

    // Test N.° 4 — Comprobar que se genera el correo
    public function test_correo_se_envia_correctamente(): void
    {
        Mail::fake();

        $this->post('/mail/enviar', [
            'destinatario' => 'alumno@example.com',
            'nombre' => 'Juan Pérez',
            'asunto' => 'Trabajo Práctico',
            'mensaje' => 'Trabajo recibido correctamente.'
        ]);

        Mail::assertSent(TestBrevoMail::class);
    }

    // Test N.° 5 — ¿Se envió a la persona correcta?
    public function test_correo_se_envia_al_destinatario_correcto(): void
    {
        Mail::fake();

        $this->post('/mail/enviar', [
            'destinatario' => 'alumno@example.com',
            'nombre' => 'Juan Pérez',
            'asunto' => 'Trabajo Práctico',
            'mensaje' => 'Su trabajo fue recibido.'
        ]);

        Mail::assertSent(
            TestBrevoMail::class,
            function ($mail) {
                return $mail->hasTo('alumno@example.com');
            }
        );
    }

    // Test N.° 6 — Verificar nombre, asunto y mensaje
    public function test_mailable_recibe_los_datos_correctos(): void
    {
        Mail::fake();

        $this->post('/mail/enviar', [
            'destinatario' => 'alumno@example.com',
            'nombre' => 'María López',
            'asunto' => 'Aviso importante',
            'mensaje' => 'La clase comienza a las 14 horas.'
        ]);

        Mail::assertSent(
            TestBrevoMail::class,
            function ($mail) {
                return
                    $mail->nombre === 'María López' &&
                    $mail->asunto === 'Aviso importante' &&
                    $mail->mensaje === 'La clase comienza a las 14 horas.';
            }
        );
    }

    // Test N.° 7 — No enviar si falla la validación
    public function test_no_se_envia_correo_si_los_datos_son_invalidos(): void
    {
        Mail::fake();

        $this->post('/mail/enviar', [
            'destinatario' => 'correo-invalido',
            'nombre' => '',
            'asunto' => '',
            'mensaje' => ''
        ]);

        Mail::assertNothingSent();
    }

    // Actividad para los alumnos (punto 17)
    public function test_telefono_no_puede_tener_menos_de_8_caracteres(): void
    {
        $response = $this->post('/mail/enviar', [
            'destinatario' => 'alumno@example.com',
            'nombre' => 'Juan Pérez',
            'telefono' => '123',
            'asunto' => 'Prueba',
            'mensaje' => 'Mensaje de prueba'
        ]);

        $response->assertSessionHasErrors('telefono');
    }

    public function test_telefono_valido_no_genera_error_de_validacion(): void
    {
        Mail::fake();

        $response = $this->post('/mail/enviar', [
            'destinatario' => 'alumno@example.com',
            'nombre' => 'Juan Pérez',
            'telefono' => '3704123456',
            'asunto' => 'Prueba',
            'mensaje' => 'Mensaje de prueba'
        ]);

        $response->assertSessionDoesntHaveErrors('telefono');
    }
}
