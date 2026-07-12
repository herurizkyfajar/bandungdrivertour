<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewInvoiceNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        $this->invoice->loadMissing(['booking.service', 'booking.vehicle', 'booking.mitra']);
    }

    public function envelope(): Envelope
    {
        $booking = $this->invoice->booking;
        $invoiceNo = $this->invoice->invoice_number ?? ('#' . $this->invoice->id);
        $name = $booking?->customer_name ?? 'Customer';

        return new Envelope(
            subject: 'Invoice Baru: ' . $invoiceNo . ' - ' . $name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice_created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
