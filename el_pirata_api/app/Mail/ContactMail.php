<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $link;
    public function __construct($data)
    {
        $this->data = $data;
        $this->link = env('APP_FRONT');
    }

    public function build()
    {
        return $this->subject('Nouveau message de contact: ' . $this->data['subject'])
            ->view('emails.contact')
            ->with([
                'data' => $this->data,
                'app_url' => $this->link
            ]);
        ;
    }
}
