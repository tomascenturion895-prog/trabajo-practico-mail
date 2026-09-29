<?php

namespace App\Http\Controllers;

use App\Mail\TestBrevoMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function formulario()
    {
        return view('mail.formulario');
    }

    public function enviar(Request $request)
    {
        $datos = $request->validate([
            'destinatario' => 'required|email',
            'nombre'       => 'required|string|max:100',
            'telefono'     => 'nullable|string|min:8',
            'asunto'       => 'required|string|max:150',
            'mensaje'      => 'required|string|max:5000',
        ]);

        try {

            Mail::to($datos['destinatario'])
                ->send(
                    new TestBrevoMail(
                        $datos['nombre'],
                        $datos['asunto'],
                        $datos['mensaje']
                    )
                );

            return back()->with(
                'success',
                'El correo fue enviado correctamente.'
            );

        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No se pudo enviar el correo. Verifique la configuración SMTP.'
                );
        }
    }
}
