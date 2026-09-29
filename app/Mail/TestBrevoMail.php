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

    public function __construct($nombre, $asunto, $mensaje)
    {
        $this->nombre = $nombre;
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        return $this
            ->subject($this->asunto)
            ->view('emails.test-brevo');
    }
}
