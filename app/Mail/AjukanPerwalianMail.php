<?php

namespace App\Mail;

use App\Models\Advise;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AjukanPerwalianMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $perwalian;
    public function __construct(Advise $wali)
    {
        $this->perwalian = $wali;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengajuan Perwalian Mahasiswa',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.perwalian-diajukan',
            with: [
                'wali' => $this->perwalian,
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
