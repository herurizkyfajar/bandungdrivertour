<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\TelegramMessage;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    public function __construct(
        protected TelegramNotificationService $notify,
        protected InvoicePdfService $pdf,
    ) {
    }

    public function handleUpdate(array $update): void
    {
        $message = $update['message'] ?? $update['edited_message'] ?? null;
        if (!is_array($message)) {
            return;
        }

        $text = trim((string) ($message['text'] ?? ''));
        if ($text === '') {
            return;
        }

        $chatId = (string) ($message['chat']['id'] ?? '');
        $messageId = (int) ($message['message_id'] ?? 0);

        if ($chatId === '' || !in_array($chatId, $this->notify->chatIds(), true)) {
            Log::info('Telegram webhook: chat tidak terdaftar, pesan diabaikan.', [
                'chat_id' => $chatId,
                'text' => mb_substr($text, 0, 50),
            ]);

            return;
        }

        if (!filter_var(env('TELEGRAM_COMMANDS_ENABLED', true), FILTER_VALIDATE_BOOL)) {
            return;
        }

        $commandText = $this->normalizeCommandText($text);

        if (in_array($commandText, ['start', 'help', 'bantuan', 'menu', '?'], true)) {
            $this->reply($this->helpText(), $chatId, $messageId);

            return;
        }

        $invoice = $this->resolveInvoice($message, $text);
        $action = $this->extractAction($text, $invoice !== null);

        if ($action === null) {
            $this->reply(
                "⚠️ Perintah tidak dikenali: <code>" . $this->notify->escape($commandText) . "</code>\nKirim <code>help</code> untuk melihat daftar perintah.",
                $chatId,
                $messageId
            );

            return;
        }

        if ($invoice === null) {
            $this->reply(
                "⚠️ Invoice tidak ditemukan atau tidak bisa ditentukan dari pesan Anda.\n"
                . "Gunakan format <code>INV-20260929-00178/" . $action . "</code> atau reply pesan notifikasi booking.",
                $chatId,
                $messageId
            );

            return;
        }

        $invoice->loadMissing('booking.services', 'booking.vehicle', 'booking.group');

        match ($action) {
            'info' => $this->handleInfo($invoice, $chatId, $messageId),
            'download' => $this->handleDownload($invoice, $chatId, $messageId),
            default => $this->handleUpdatePrice($invoice, $action, $chatId, $messageId),
        };
    }

    protected function normalizeCommandText(string $text): string
    {
        $text = preg_replace('/^\/([a-zA-Z]+)(@[a-zA-Z0-9_]+)?/', '$1', trim($text));

        return mb_strtolower(trim($text));
    }

    protected function extractAction(string $text, bool $hasInvoiceContext): ?string
    {
        $text = preg_replace('/INV[-\s]?\d{6,8}[-\s]?\d{1,5}/i', '', $text);
        $action = trim($text, " \t\n\r\0\x0B/,:;-");

        if ($action === '') {
            return 'info';
        }

        $action = mb_strtolower($action);

        if (in_array($action, ['info', 'detail', 'detailnya'], true)) {
            return 'info';
        }

        if (in_array($action, ['download', 'unduh', 'pdf', 'file'], true)) {
            return 'download';
        }

        if (preg_match('/^[\d.,\s]+$/', $action)) {
            return $action;
        }

        if (!$hasInvoiceContext) {
            // tanpa nomor invoice dan bukan perintah dikenal → kemungkinan teks bantuan
            if (in_array($this->normalizeCommandText($text), ['start', 'help', 'bantuan', 'menu', '?'], true)) {
                return 'info';
            }
        }

        return null;
    }

    protected function resolveInvoice(array $message, string $text): ?Invoice
    {
        $reply = $message['reply_to_message'] ?? null;
        $chatId = (string) ($message['chat']['id'] ?? '');

        if (is_array($reply)) {
            $mapped = TelegramMessage::query()
                ->where('chat_id', $chatId)
                ->where('message_id', (int) ($reply['message_id'] ?? 0))
                ->first();

            if ($mapped?->invoice_id) {
                $invoice = Invoice::find($mapped->invoice_id);
                if ($invoice) {
                    return $invoice;
                }
            }

            $invoice = $this->findByNumberInText((string) ($reply['text'] ?? ''));
            if ($invoice) {
                return $invoice;
            }
        }

        return $this->findByNumberInText($text);
    }

    protected function findByNumberInText(string $text): ?Invoice
    {
        if (!preg_match('/INV[-\s]?(\d{6,8})[-\s]?(\d{1,5})/i', $text, $m)) {
            return null;
        }

        $normalized = strtoupper('INV' . $m[1] . $m[2]);

        $invoice = Invoice::query()
            ->whereRaw("REPLACE(REPLACE(UPPER(invoice_number), '-', ''), ' ', '') = ?", [$normalized])
            ->first();

        if ($invoice) {
            $invoice->loadMissing('booking.services', 'booking.vehicle', 'booking.group');
        }

        return $invoice;
    }

    protected function parseAmount(string $raw): ?float
    {
        $raw = trim($raw);

        if (!preg_match('/^\d{1,3}([.,\s]\d{3})*$|^\d+$/', $raw)) {
            return null;
        }

        $clean = preg_replace('/\D/', '', $raw);
        if ($clean === '') {
            return null;
        }

        $amount = (float) $clean;

        return $amount > 0 ? $amount : null;
    }

    protected function handleUpdatePrice(Invoice $invoice, string $action, string $chatId, int $messageId): void
    {
        $amount = $this->parseAmount($action);

        if ($amount === null) {
            $this->reply(
                "⚠️ Format biaya tidak dikenali: <code>" . $this->notify->escape($action) . "</code>\n"
                . "Contoh: <code>" . $invoice->invoice_number . "/1500000</code> atau <code>1.500.000</code>",
                $chatId,
                $messageId,
                $invoice
            );

            return;
        }

        $booking = $invoice->booking;
        if (!$booking) {
            $this->reply('⚠️ Invoice ini tidak terhubung ke booking.', $chatId, $messageId, $invoice);

            return;
        }

        $old = (float) ($booking->price ?? 0);
        $booking->price = $amount;

        try {
            $booking->save();
        } catch (\Throwable $e) {
            Log::warning('Telegram bot: gagal update harga booking: ' . $e->getMessage());
            $this->reply('⚠️ Gagal menyimpan perubahan: ' . $this->notify->escape($e->getMessage()), $chatId, $messageId, $invoice);

            return;
        }

        $this->reply(
            "✅ <b>Harga booking diperbarui</b>\n"
            . "📄 " . $this->notify->escape($invoice->invoice_number) . "\n"
            . "👤 " . $this->notify->escape((string) $booking->customer_name) . "\n"
            . "💰 " . $this->formatAmount($old) . " → <b>" . $this->formatAmount($amount) . "</b>",
            $chatId,
            $messageId,
            $invoice,
            $booking
        );
    }

    protected function handleInfo(Invoice $invoice, string $chatId, int $messageId): void
    {
        $booking = $invoice->booking;

        $lines = [
            "📄 <b>" . $this->notify->escape($invoice->invoice_number) . "</b> (" . $this->notify->escape((string) $invoice->status) . ")",
        ];

        if ($booking) {
            $lines[] = "👤 " . $this->notify->escape((string) $booking->customer_name)
                . ' (' . $this->notify->escape((string) $booking->contact_number) . ')';
            $lines[] = "📅 " . $this->notify->escape($booking->booking_date?->format('d-m-Y') ?? '-')
                . ' - ' . $this->notify->escape($booking->end_date?->format('d-m-Y') ?? '-')
                . ' (' . $this->notify->escape((string) $booking->pickup_time) . ')';
            $lines[] = "📍 " . $this->notify->escape((string) $booking->pickup_location);
            $lines[] = "🚗 " . $this->notify->escape(
                $booking->vehicle ? ($booking->vehicle->make . ' ' . $booking->vehicle->model) : 'Tidak Ada'
            );
            $lines[] = "💰 Harga booking: <b>" . $this->formatAmount((float) ($booking->price ?? 0)) . '</b>';
            $lines[] = "🔗 " . $this->notify->escape(route('invoice.show', $invoice));
        }

        $this->reply(implode("\n", $lines), $chatId, $messageId, $invoice, $booking);
    }

    protected function handleDownload(Invoice $invoice, string $chatId, int $messageId): void
    {
        try {
            $document = $this->pdf->contents($invoice);
        } catch (\Throwable $e) {
            Log::warning('Telegram bot: gagal generate PDF: ' . $e->getMessage());
            $this->reply('⚠️ Gagal membuat PDF: ' . $this->notify->escape($e->getMessage()), $chatId, $messageId, $invoice);

            return;
        }

        $caption = "📄 <b>" . $this->notify->escape($invoice->invoice_number) . "</b>\n"
            . "👤 " . $this->notify->escape((string) ($invoice->booking->customer_name ?? '-'));

        $sent = $this->notify->sendDocument(
            $document['filename'],
            $document['binary'],
            $caption,
            $invoice,
            $invoice->booking,
            $messageId,
            $chatId
        );

        if (!$sent) {
            $this->reply(
                '⚠️ Gagal mengirim file: ' . $this->notify->escape((string) $this->notify->lastError()),
                $chatId,
                $messageId,
                $invoice
            );
        }
    }

    protected function reply(string $text, string $chatId, int $messageId, ?Invoice $invoice = null, ?Booking $booking = null): bool
    {
        return $this->notify->sendMessage($text, $invoice, $booking, $messageId, $chatId);
    }

    protected function formatAmount(float $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }

    protected function helpText(): string
    {
        return implode("\n", [
            '<b>Perintah Bot Booking</b>',
            '━━━━━━━━━━━━━━━',
            'Kirim salah satu:',
            '• <code>INV-20260929-00178</code> → info invoice',
            '• <code>INV-20260929-00178/1500000</code> → update harga booking',
            '• <code>INV-20260929-00178/download</code> → kirim PDF invoice',
            '',
            'Atau <b>reply</b> pesan notifikasi booking lalu kirim:',
            '• <code>info</code>',
            '• <code>1500000</code>',
            '• <code>download</code>',
        ]);
    }
}
