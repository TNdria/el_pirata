<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;


    public $user;
    public $link;
    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->link = env('APP_FRONT');
    }

    public function build()
    {
        return $this->subject('Votre code de vérification')
            ->view('emails.two-factor-code')->with([
                    'user' => $this->user,
                    'app_url' => $this->link
                ]);
        ;
    }
}
