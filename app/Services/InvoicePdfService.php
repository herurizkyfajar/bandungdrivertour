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

    public function pathFor(Invoice $invoice): string
    {
        return 'invoices/v2/' . $this->safeFilename($invoice);
    }

    public function purge(Invoice $invoice): void
    {
        $path = $this->pathFor($invoice);

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function generate(Invoice $invoice): array
    {
        $invoice->loadMissing('booking.services', 'booking.vehicle', 'booking.mitra');

        $filename = $this->safeFilename($invoice);
        $path = 'invoices/v2/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            $html = view('invoices.pdf', array_merge(
                ['invoice' => $invoice],
                $this->imageData($invoice),
            ))->render();
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

    /**
     * Resolve images used by the invoice PDF into base64 data URIs so the
     * renderer never depends on remote HTTP fetching (which fails when
     * APP_URL points to localhost or is unreachable from the server).
     */
    protected function imageData(Invoice $invoice): array
    {
        $settings = \App\Models\InvoiceSetting::instance();

        $groups = \App\Models\Group::whereNotNull('logo_path')
            ->orderBy('name')
            ->get()
            ->map(fn ($g) => [
                'name' => $g->name,
                'src' => $this->storageDataUri($g->logo_path),
            ])
            ->filter(fn ($g) => $g['src'] !== null)
            ->values();

        $signature = $this->storageDataUri($settings->signature_path)
            ?? $this->publicDataUri('ttd_aldi.png');

        return [
            'watermarkSrc' => $this->publicDataUri('Logo-Bandung-Driver-Tour.webp'),
            'groupLogos' => $groups,
            'signatureSrc' => $signature,
        ];
    }

    protected function storageDataUri(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        $mime = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'image/png',
        };

        $contents = Storage::disk('public')->get($path);
        if ($contents === null || $contents === '') {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }

    protected function publicDataUri(string $relativePath): ?string
    {
        $full = public_path($relativePath);
        if (!is_file($full)) {
            return null;
        }

        $mime = match (strtolower(pathinfo($full, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'image/png',
        };

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($full));
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
