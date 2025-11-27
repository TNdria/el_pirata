<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class fortgotpassword extends Mailable
{
    use Queueable, SerializesModels;

    use Queueable, SerializesModels;

    public $urlRecoveryPassword;  // ✅ Ajout de la variable accessible dans la vue
    public $link;
    /**
     * Créer une nouvelle instance du message.
     */
    public function __construct($data)
    {
        if ($data->is_admin) {
            $this->urlRecoveryPassword = env('APP_ADMIN') . "/recovery_password?user=" . $data->user->createToken('auth_token')->plainTextToken;
        }

        $this->link = env('APP_FRONT');
        // $this->verificationUrl = "/verify-email?user=" . $user->createToken('auth_token')->plainTextToken;
    }

    /**
     * Construire le message.
     */
    public function build()
    {
        return $this->subject('Mot de passe oublier')
            ->view('emails.forgot_password')  // Assurez-vous que ce fichier existe bien
            ->with([
                'urlRecoveryPassword' => $this->urlRecoveryPassword,
                'app_url' => $this->link
            ]);
    }
}
