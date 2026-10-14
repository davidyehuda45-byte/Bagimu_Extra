<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UndanganAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nama,
        public string $username,
        public string $password,
        public string $peran,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Undangan Akun Staf — '.config('app.name', 'Bagimu Extra'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.undangan-admin',
        );
    }
}
