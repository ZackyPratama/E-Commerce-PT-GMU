<?php

namespace App\Mail;

use App\Models\RFQ;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RFQRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public RFQ $rfq;

    public function __construct(RFQ $rfq)
    {
        $this->rfq = $rfq->load(['items.product', 'customer']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'RFQ Ditolak - ' . $this->rfq->rfq_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.rfq-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
