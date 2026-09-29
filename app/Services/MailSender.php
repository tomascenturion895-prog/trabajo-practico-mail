<?php

namespace App\Services;

use App\Mail\TestBrevoMail;
use App\Models\SentMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/**
 * Envía el correo y deja registro en el historial.
 * Lo usan tanto el formulario Livewire como POST /mail/enviar.
 */
class MailSender
{
    /**
     * @param  array{para: array, cc?: array, cco?: array, nombre: string, telefono?: ?string, asunto: string, mensaje: string}  $datos
     * @param  array  $archivos  UploadedFile / TemporaryUploadedFile
     *
     * @throws Throwable si el envío falla (el intento igual queda registrado como "fallido")
     */
    public function enviar(array $datos, array $archivos = []): SentMail
    {
        $cc = $datos['cc'] ?? [];
        $cco = $datos['cco'] ?? [];

        $adjuntos = [];
        foreach ($archivos as $archivo) {
            $adjuntos[] = [
                'path' => $archivo->getRealPath(),
                'name' => $archivo->getClientOriginalName(),
                'mime' => $archivo->getMimeType(),
                'size' => $archivo->getSize(),
            ];
        }

        $excepcion = null;

        try {
            $envio = Mail::to($datos['para']);

            if ($cc) {
                $envio->cc($cc);
            }
            if ($cco) {
                $envio->bcc($cco);
            }

            $envio->send(new TestBrevoMail(
                $datos['nombre'],
                $datos['asunto'],
                $datos['mensaje'],
                $adjuntos
            ));
        } catch (Throwable $e) {
            $excepcion = $e;
            report($e);
        }

        $registro = $this->registrar($datos, $adjuntos, $excepcion);

        if ($excepcion) {
            throw $excepcion;
        }

        return $registro;
    }

    private function registrar(array $datos, array $adjuntos, ?Throwable $error): ?SentMail
    {
        try {
            return SentMail::create([
                'user_id' => Auth::id(),
                'destinatario' => implode(', ', $datos['para']),
                'nombre' => $datos['nombre'],
                'telefono' => $datos['telefono'] ?? null,
                'cc' => ($datos['cc'] ?? []) ? implode(', ', $datos['cc']) : null,
                'cco' => ($datos['cco'] ?? []) ? implode(', ', $datos['cco']) : null,
                'asunto' => $datos['asunto'],
                'mensaje' => $datos['mensaje'],
                'adjuntos' => $adjuntos
                    ? array_map(fn ($a) => ['name' => $a['name'], 'size' => $a['size']], $adjuntos)
                    : null,
                'estado' => $error ? SentMail::FALLIDO : SentMail::ENVIADO,
                'error' => $error ? Str::limit($error->getMessage(), 500) : null,
            ]);
        } catch (Throwable $e) {
            // Que un problema con el historial nunca impida el envío ni oculte el error real.
            report($e);

            return null;
        }
    }
}
