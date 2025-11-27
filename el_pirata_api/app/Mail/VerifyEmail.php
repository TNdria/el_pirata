<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;  // ✅ Ajout de la variable accessible dans la vue
    public $link;
    /**
     * Créer une nouvelle instance du message.
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->verificationUrl = env('APP_FRONT') . "/verify-email?user=" . $user->createToken('auth_token')->plainTextToken;
        $this->link = env('APP_FRONT');
        // $this->verificationUrl = "/verify-email?user=" . $user->createToken('auth_token')->plainTextToken;
    }

    /**
     * Construire le message.
     */
    public function build()
    {
        return $this->subject('Confirmez votre adresse e-mail 🏴‍☠️')
            ->view('emails.verification-code')  // Assurez-vous que ce fichier existe bien
            ->with([
                'verificationUrl' => $this->verificationUrl,
                'app_url' => $this->link
            ]);
    }
}
