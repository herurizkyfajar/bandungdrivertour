<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class InvoiceController extends Controller
{
    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function sendWhatsapp(Request $request, Invoice $invoice)
    {
        $booking = $invoice->booking;
        $rawPhone = (string) ($booking->contact_number ?? '');
        $phone = preg_replace('/\D/', '', $rawPhone);
        if (!$phone) {
            return back()->with('error', 'Nomor WhatsApp customer tidak valid.');
        }
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        $safeInvoiceNumber = preg_replace('/[\\\\\/:*?"<>|]+/', '', (string) $invoice->invoice_number);
        $safeInvoiceNumber = trim(preg_replace('/\s+/', ' ', $safeInvoiceNumber));
        if ($safeInvoiceNumber === '') {
            $safeInvoiceNumber = 'invoice';
        }

        $bookingName = trim(preg_replace('/[\\\\\/:*?"<>|]+/', '', (string) ($booking->customer_name ?? 'Customer')));
        $bookingName = preg_replace('/\s+/', ' ', $bookingName);
        if ($bookingName === '') {
            $bookingName = 'Customer';
        }

        $filename = $safeInvoiceNumber . '_' . $bookingName . '.pdf';
        $path = 'invoices/' . $filename;
        if (!Storage::disk('public')->exists($path)) {
            try {
                $html = view('invoices.show', ['invoice' => $invoice])->render();
                $dompdf = new \Dompdf\Dompdf([
                    'isRemoteEnabled' => true,
                ]);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $pdf = $dompdf->output();
                Storage::disk('public')->put($path, $pdf);
            } catch (\Throwable $e) {
                Log::error('Generate PDF gagal: ' . $e->getMessage());
                return back()->with('error', 'Gagal membuat PDF invoice. Pastikan paket dompdf terinstall.');
            }
        }
        $url = Storage::disk('public')->url($path);

        $token = env('WHATSAPP_TOKEN');
        $phoneId = env('WHATSAPP_PHONE_ID');
        if (!$token || !$phoneId) {
            return back()->with('error', 'Konfigurasi WhatsApp belum diset (WHATSAPP_TOKEN, WHATSAPP_PHONE_ID).');
        }
        try {
            $client = new Client([
                'base_uri' => 'https://graph.facebook.com/v18.0/',
                'timeout' => 20,
            ]);
            $resp = $client->post($phoneId . '/messages', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'document',
                    'document' => [
                        'link' => $url,
                        'filename' => $filename,
                    ],
                ],
            ]);
            if ($resp->getStatusCode() >= 200 && $resp->getStatusCode() < 300) {
                return back()->with('success', 'Invoice PDF telah dikirim ke WhatsApp customer.');
            }
            return back()->with('error', 'Gagal mengirim WhatsApp: status ' . $resp->getStatusCode());
        } catch (\Throwable $e) {
            Log::error('Kirim WhatsApp gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim WhatsApp: ' . $e->getMessage());
        }
    }
}
