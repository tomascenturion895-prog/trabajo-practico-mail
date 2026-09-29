<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\WelcomeUserMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Número de reintentos en caso de error en el SMTP
    public $tries = 3;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        // Enviar el mailable pasando los datos del usuario
        Mail::to($this->user->email)->send(new WelcomeUserMail([
            'name' => $this->user->name
        ]));
    }
}
