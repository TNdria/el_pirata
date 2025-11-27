<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VipCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $vipCode;
    public $hunting;
    public $rank;
    public $link;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $vipCode, $hunting, $rank)
    {
        $this->user = $user;
        $this->vipCode = $vipCode;
        $this->hunting = $hunting;
        $this->rank = $rank;
        $this->link = env('APP_FRONT');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🏆 Félicitations ! Votre code VIP El Pirata',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.vip-code',
            with: [
                'user' => $this->user,
                'vipCode' => $this->vipCode,
                'hunting' => $this->hunting,
                'rank' => $this->rank,
                'app_url' => $this->link
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

