<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $userData;

    public function __construct(array $userData)
    {
        $this->userData = $userData;
    }

    public function build(): self
    {
        return $this->subject('¡Bienvenido a nuestra plataforma!')
                    ->view('emails.welcome')
                    ->with(['nombre' => $this->userData['name']]);
    }
}
