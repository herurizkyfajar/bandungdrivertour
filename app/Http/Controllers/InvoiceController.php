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
        $invoice->load('booking.services', 'booking.vehicle', 'booking.mitra');
        return view('invoices.show', compact('invoice'));
    }

    public function sendWhatsapp(Request $request, Invoice $invoice)
    {
        $invoice->load('booking.services', 'booking.vehicle', 'booking.mitra');
        $booking = $invoice->booking;
        $rawPhone = (string) ($booking->contact_number ?? '');
        $phone = preg_replace('/\D/', '', $rawPhone);
        if (!$phone) {
            return back()->with('error', 'Nomor WhatsApp customer tidak valid.');
        }
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $generated = app(\App\Services\InvoicePdfService::class)->generate($invoice);
        } catch (\Throwable $e) {
            Log::error('Generate PDF gagal: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat PDF invoice. Pastikan paket dompdf terinstall.');
        }

        $path = $generated['path'];
        $filename = $generated['filename'];
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
