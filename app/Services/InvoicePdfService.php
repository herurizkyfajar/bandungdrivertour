<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    public function safeFilename(Invoice $invoice): string
    {
        $invoiceNumber = preg_replace('/[\\\\\/:*?"<>|]+/', '', (string) $invoice->invoice_number);
        $invoiceNumber = trim(preg_replace('/\s+/', ' ', $invoiceNumber));
        if ($invoiceNumber === '') {
            $invoiceNumber = 'invoice';
        }

        $customer = trim(preg_replace('/[\\\\\/:*?"<>|]+/', '', (string) ($invoice->booking->customer_name ?? 'Customer')));
        $customer = preg_replace('/\s+/', ' ', $customer);
        if ($customer === '') {
            $customer = 'Customer';
        }

        return $invoiceNumber . '_' . $customer . '.pdf';
    }

    public function generate(Invoice $invoice): array
    {
        $invoice->loadMissing('booking.services', 'booking.vehicle', 'booking.mitra');

        $filename = $this->safeFilename($invoice);
        $path = 'invoices/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            $html = view('invoices.show', ['invoice' => $invoice])->render();
            $dompdf = new \Dompdf\Dompdf([
                'isRemoteEnabled' => true,
            ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            Storage::disk('public')->put($path, $dompdf->output());
        }

        return ['path' => $path, 'filename' => $filename];
    }

    public function contents(Invoice $invoice): array
    {
        $generated = $this->generate($invoice);

        return [
            'filename' => $generated['filename'],
            'binary' => (string) Storage::disk('public')->get($generated['path']),
        ];
    }
}
