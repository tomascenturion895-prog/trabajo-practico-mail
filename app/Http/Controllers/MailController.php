<?php

namespace App\Http\Controllers;

use App\Services\MailSender;
use Illuminate\Http\Request;
use Throwable;

class MailController extends Controller
{
    // Muestra el formulario de envío de correo (componente Livewire)
    public function formulario()
    {
        return view('mail.formulario');
    }

    // Procesa el envío tradicional (POST). El formulario Livewire usa el mismo servicio.
    public function enviar(Request $request, MailSender $sender)
    {
        $datos = $request->validate([
            'destinatario' => 'required|email',
            'nombre' => 'required|string|max:100',
            'telefono' => 'nullable|string|min:8',
            'asunto' => 'required|string|max:150',
            'mensaje' => 'required|string|max:5000',
        ]);

        try {
            $sender->enviar([
                'para' => [$datos['destinatario']],
                'nombre' => $datos['nombre'],
                'telefono' => $datos['telefono'] ?? null,
                'asunto' => $datos['asunto'],
                'mensaje' => $datos['mensaje'],
            ]);

            return back()->with('success', 'El correo fue enviado correctamente.');
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'No se pudo enviar el correo. Verifique la configuración SMTP.');
        }
    }
}
