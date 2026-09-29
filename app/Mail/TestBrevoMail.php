<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestBrevoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $asunto;
    public $mensaje;

    /** @var array<int, array{path: string, name: string, mime: ?string}> */
    public array $adjuntos;

    public function __construct($nombre, $asunto, $mensaje, array $adjuntos = [])
    {
        $this->nombre = $nombre;
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->adjuntos = $adjuntos;
    }

    public function build()
    {
        $mail = $this->subject($this->asunto)
            ->view('emails.test-brevo');

        foreach ($this->adjuntos as $adjunto) {
            $mail->attach($adjunto['path'], array_filter([
                'as' => $adjunto['name'],
                'mime' => $adjunto['mime'] ?? null,
            ]));
        }

        return $mail;
    }
}
