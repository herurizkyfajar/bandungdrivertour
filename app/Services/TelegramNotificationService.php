<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\TelegramMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    public const TEMPLATE_FILE = 'telegram-message.txt';

    protected ?string $lastError = null;

    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function enabled(): bool
    {
        return filter_var(env('TELEGRAM_ENABLED', false), FILTER_VALIDATE_BOOL)
            && trim((string) env('TELEGRAM_BOT_TOKEN', '')) !== ''
            && trim((string) env('TELEGRAM_CHAT_ID', '')) !== '';
    }

    public function chatIds(): array
    {
        $raw = (string) env('TELEGRAM_CHAT_ID', '');

        return collect(explode(',', $raw))
            ->map(static fn ($v) => trim($v))
            ->filter()
            ->values()
            ->all();
    }

    public function parseMode(): string
    {
        $mode = trim((string) env('TELEGRAM_PARSE_MODE', 'HTML'));

        return in_array($mode, ['HTML', 'None'], true) ? $mode : 'HTML';
    }

    public function templatePath(): string
    {
        return storage_path('app/' . self::TEMPLATE_FILE);
    }

    public function defaultTemplate(): string
    {
        return implode("\n", [
            '<b>BOOKING BARU MASUK</b>',
            '━━━━━━━━━━━━━━━',
            '👤 {{customer_name}} ({{contact_number}})',
            '🏢 {{company}}',
            '📍 {{pickup_location}}',
            '📅 {{booking_date}} - {{end_date}} ({{pickup_time}})',
            '🚗 {{vehicle_name}}',
            '🧾 {{service_name}}',
            '👥 {{passengers}} penumpang',
            '💰 {{price}}',
            '📄 {{invoice_number}}',
            '🔗 {{booking_url}}',
        ]);
    }

    public function template(): string
    {
        $path = $this->templatePath();

        if (is_file($path)) {
            $content = (string) file_get_contents($path);

            if (trim($content) !== '') {
                return $content;
            }
        }

        return $this->defaultTemplate();
    }

    public function saveTemplate(string $template): bool
    {
        $dir = dirname($this->templatePath());
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            return false;
        }

        return file_put_contents($this->templatePath(), $template) !== false;
    }

    public function placeholders(): array
    {
        $keys = [
            'customer_name' => 'Nama customer',
            'contact_number' => 'Nomor kontak',
            'company' => 'Nama grup/perusahaan (Personal jika umum)',
            'passengers' => 'Jumlah penumpang',
            'country' => 'Negara asal',
            'pickup_location' => 'Lokasi penjemputan',
            'pickup_address' => 'Alamat penjemputan (EN)',
            'booking_date' => 'Tanggal mulai (dd-mm-yyyy)',
            'end_date' => 'Tanggal selesai (dd-mm-yyyy)',
            'pickup_time' => 'Jam penjemputan (HH.mm)',
            'vehicle_name' => 'Kendaraan',
            'service_name' => 'Layanan yang dipilih',
            'price' => 'Harga (format ribuan)',
            'payment_plan' => 'Metode pembayaran',
            'status' => 'Status booking',
            'invoice_number' => 'Nomor invoice',
            'invoice_amount' => 'Jumlah invoice',
            'invoice_status' => 'Status invoice (paid/unpaid)',
            'info_source' => 'Sumber informasi',
            'booking_url' => 'Link invoice/booking',
            'booking_id' => 'ID booking',
            'created_at' => 'Waktu booking dibuat',
        ];

        $open = chr(123) . chr(123);
        $close = chr(125) . chr(125);

        $placeholders = [];
        foreach ($keys as $key => $desc) {
            $placeholders[] = [
                'key' => $key,
                'label' => $open . $key . $close,
                'desc' => $desc,
            ];
        }

        return $placeholders;
    }

    protected function dataFor(Booking $booking, ?Invoice $invoice = null): array
    {
        if ($booking->exists) {
            $booking->loadMissing(['vehicle', 'services', 'group']);
        }

        $infoSource = (string) ($booking->info_source ?? '-');
        if (!empty($booking->info_source_other)) {
            $infoSource .= ' (' . $booking->info_source_other . ')';
        }

        $paymentPlans = [
            'down_payment' => 'Down Payment',
            'payment_full_transfer' => 'Full Transfer',
            'payment_full_on_driver' => 'Full on Driver',
        ];

        return [
            'customer_name' => $booking->customer_name ?? '-',
            'contact_number' => $booking->contact_number ?? '-',
            'company' => $booking->group ? $booking->group->name : 'Personal / Umum',
            'passengers' => $booking->number_of_passengers ?? '-',
            'country' => $booking->country_of_origin ?? '-',
            'pickup_location' => $booking->pickup_location ?? '-',
            'pickup_address' => $booking->pickup_address_en ?? '-',
            'booking_date' => $booking->booking_date ? date('d-m-Y', strtotime($booking->booking_date)) : '-',
            'end_date' => $booking->end_date ? date('d-m-Y', strtotime($booking->end_date)) : '-',
            'pickup_time' => $booking->pickup_time ? date('H.i', strtotime($booking->pickup_time)) : '-',
            'vehicle_name' => $booking->vehicle ? ($booking->vehicle->make . ' ' . $booking->vehicle->model) : 'Tidak Ada',
            'service_name' => $booking->services->pluck('name')->implode(', ') ?: 'Tidak Ada',
            'price' => number_format((float) ($booking->price ?? 0), 0, ',', '.'),
            'payment_plan' => $paymentPlans[$booking->payment_plan ?? ''] ?? ($booking->payment_plan ?? '-'),
            'status' => $booking->status ?? '-',
            'invoice_number' => $invoice->invoice_number ?? '-',
            'invoice_amount' => $invoice ? number_format((float) $invoice->amount, 0, ',', '.') : '-',
            'invoice_status' => $invoice->status ?? '-',
            'info_source' => $infoSource !== '' ? $infoSource : '-',
            'booking_url' => ($invoice && $invoice->exists) ? route('invoice.show', $invoice) : '-',
            'booking_id' => $booking->id ?? '-',
            'created_at' => $booking->created_at ? $booking->created_at->format('d-m-Y H:i') : '-',
        ];
    }

    public function sampleData(): array
    {
        return [
            'customer_name' => 'Budi Santoso',
            'contact_number' => '+62 812-0000-0000',
            'company' => 'PT Contoh Wisata',
            'passengers' => 4,
            'country' => 'Indonesia',
            'pickup_location' => 'Hotel Hibiscus, Bandung',
            'pickup_address' => 'Jl. Cihampelas No. 1, Bandung',
            'booking_date' => date('d-m-Y'),
            'end_date' => date('d-m-Y', strtotime('+2 days')),
            'pickup_time' => '08.30',
            'vehicle_name' => 'Toyota Avanza',
            'service_name' => 'Private Tour, Airport Transfer',
            'price' => '1.500.000',
            'payment_plan' => 'Down Payment',
            'status' => 'baru_masuk',
            'invoice_number' => 'INV-' . date('Ymd') . '-00161',
            'invoice_amount' => '1.500.000',
            'invoice_status' => 'unpaid',
            'info_source' => 'Instagram',
            'booking_url' => route('dashboard'),
            'booking_id' => 1,
            'created_at' => now()->format('d-m-Y H:i'),
        ];
    }

    public function renderFrom(array $data, string $template): string
    {
        foreach ($data as $key => $value) {
            $value = (string) $value;

            if ($this->parseMode() === 'HTML') {
                $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }

            $template = str_replace(
                ['{{' . $key . '}}', '{{ ' . $key . ' }}'],
                $value,
                $template
            );
        }

        return $template;
    }

    public function preview(): string
    {
        return $this->renderFrom($this->sampleData(), $this->template());
    }

    public function escape(string $value): string
    {
        if ($this->parseMode() === 'HTML') {
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        return $value;
    }

    protected function token(): string
    {
        return trim((string) env('TELEGRAM_BOT_TOKEN', ''));
    }

    protected function endpoint(string $method): string
    {
        return 'https://api.telegram.org/bot' . $this->token() . '/' . $method;
    }

    protected function request(string $method, array $payload, string $chatId, ?Invoice $invoice = null, ?Booking $booking = null): bool
    {
        if ($this->token() === '') {
            $this->lastError = 'Bot token belum diisi.';

            return false;
        }

        $payload['chat_id'] = $chatId;

        try {
            $http = Http::timeout(30)->withHeaders(['Accept' => 'application/json']);

            if (array_key_exists('document', $payload)) {
                $binary = (string) $payload['document'];
                $filename = (string) ($payload['filename'] ?? 'document.pdf');
                unset($payload['document'], $payload['filename']);

                $response = $http
                    ->attach('document', $binary, $filename)
                    ->post($this->endpoint($method), $payload);
            } else {
                $response = $http->post($this->endpoint($method), $payload);
            }

            if ($response->failed()) {
                $this->lastError = $response->json('description') ?? ('HTTP ' . $response->status());
                Log::warning('Gagal mengirim pesan Telegram ke ' . $chatId . ': ' . $this->lastError);

                return false;
            }

            $messageId = $response->json('result.message_id');
            if ($messageId && ($invoice || $booking)) {
                TelegramMessage::updateOrCreate(
                    ['chat_id' => (string) $chatId, 'message_id' => (int) $messageId],
                    ['invoice_id' => $invoice?->id, 'booking_id' => $booking?->id]
                );
            }

            return true;
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            Log::warning('Gagal mengirim pesan Telegram ke ' . $chatId . ': ' . $e->getMessage());

            return false;
        }
    }

    protected function postTo(array $payload, ?Invoice $invoice = null, ?Booking $booking = null): bool
    {
        $this->lastError = null;

        $chatIds = $this->chatIds();

        if ($this->token() === '' || empty($chatIds)) {
            $this->lastError = 'Bot token atau Chat ID belum diisi.';

            return false;
        }

        $method = array_key_exists('document', $payload) ? 'sendDocument' : 'sendMessage';
        $sent = false;
        $errors = [];

        foreach ($chatIds as $chatId) {
            if ($this->request($method, $payload, (string) $chatId, $invoice, $booking)) {
                $sent = true;
            } elseif ($this->lastError !== null) {
                $errors[] = $this->lastError;
            }
        }

        if (!$sent) {
            $this->lastError = implode('; ', $errors) ?: 'Gagal mengirim pesan.';
        }

        return $sent;
    }

    public function sendToChat(string $chatId, string $text): bool
    {
        $this->lastError = null;

        return $this->request('sendMessage', ['text' => $text] + $this->parseModePayload(), $chatId);
    }

    public function sendMessage(
        string $text,
        ?Invoice $invoice = null,
        ?Booking $booking = null,
        ?int $replyToMessageId = null,
        ?string $replyChatId = null
    ): bool {
        $payload = ['text' => $text] + $this->parseModePayload();

        if ($replyToMessageId && $replyChatId !== null
            && in_array((string) $replyChatId, $this->chatIds(), true)) {
            $payload['reply_to_message_id'] = $replyToMessageId;
            $this->lastError = null;

            return $this->request('sendMessage', $payload, (string) $replyChatId, $invoice, $booking);
        }

        return $this->postTo($payload, $invoice, $booking);
    }

    public function sendDocument(
        string $filename,
        string $binary,
        string $caption,
        ?Invoice $invoice = null,
        ?Booking $booking = null,
        ?int $replyToMessageId = null,
        ?string $replyChatId = null
    ): bool {
        $payload = [
            'document' => $binary,
            'filename' => $filename,
            'caption' => $caption,
        ] + $this->parseModePayload();

        if ($replyToMessageId && $replyChatId !== null
            && in_array((string) $replyChatId, $this->chatIds(), true)) {
            $payload['reply_to_message_id'] = $replyToMessageId;
            $this->lastError = null;

            return $this->request('sendDocument', $payload, (string) $replyChatId, $invoice, $booking);
        }

        return $this->postTo($payload, $invoice, $booking);
    }

    public function send(Booking $booking, ?Invoice $invoice = null): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        $message = trim($this->renderFrom($this->dataFor($booking, $invoice), $this->template()));

        if ($message === '') {
            $this->lastError = 'Template pesan kosong.';

            return false;
        }

        return $this->postTo(['text' => $message] + $this->parseModePayload(), $invoice, $booking);
    }

    public function sendTest(): bool
    {
        if (!$this->enabled()) {
            $this->lastError = 'Notifikasi Telegram belum aktif / token belum lengkap.';

            return false;
        }

        $message = trim($this->renderFrom($this->sampleData(), $this->template()));

        return $this->postTo(['text' => $message] + $this->parseModePayload());
    }

    protected function parseModePayload(): array
    {
        return $this->parseMode() !== 'None' ? ['parse_mode' => $this->parseMode()] : [];
    }
}
