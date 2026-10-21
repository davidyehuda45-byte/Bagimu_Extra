<?php

namespace App\Mail;

use App\Models\Eskul;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class BuktiPendaftaranMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $jadwals;
    public string $verifyUrl;

    public function __construct(
        public Registration $registration,
    ) {
        $this->jadwals = Eskul::where('name', $registration->eskul_utama)->first()?->jadwals ?? collect();
        $this->verifyUrl = url('/');
    }

    public function envelope(): Envelope
    {
        $namaSiswa = $this->registration->user?->name ?? 'Siswa';
        $eskul = $this->registration->eskul_utama;

        return new Envelope(
            subject: "Bukti Pendaftaran Ekstrakurikuler: {$eskul} — {$namaSiswa}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bukti-pendaftaran',
        );
    }
}

