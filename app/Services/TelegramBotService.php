<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\TelegramMessage;
use App\Support\EnvFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    protected string $source = 'main';

    public function __construct(
        protected TelegramNotificationService $notify,
        protected TelegramJadwalService $jadwal,
        protected InvoicePdfService $pdf,
    ) {
    }

    public function handleUpdate(array $update, string $source = 'main'): void
    {
        $this->source = $source === 'jadwal' ? 'jadwal' : 'main';

        $message = $update['message'] ?? $update['edited_message'] ?? null;
        if (!is_array($message)) {
            return;
        }

        $text = trim((string) ($message['text'] ?? ''));

        $chatId = (string) ($message['chat']['id'] ?? '');
        $messageId = (int) ($message['message_id'] ?? 0);

        $migrateTo = (string) ($message['migrate_to_chat_id'] ?? '');
        if ($migrateTo !== '') {
            $this->handleChatMigration($chatId, $migrateTo);

            return;
        }

        if ($text === '') {
            return;
        }

        $allowedChatIds = $this->source === 'jadwal' ? $this->jadwal->chatIds() : $this->notify->chatIds();

        if ($chatId === '' || !in_array($chatId, $allowedChatIds, true)) {
            Log::info('Telegram webhook: chat tidak terdaftar, pesan diabaikan.', [
                'chat_id' => $chatId,
                'source' => $this->source,
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

        if (in_array($commandText, ['jadwal'], true)) {
            $this->handleJadwalCommand($text, $chatId, $messageId);

            return;
        }

        if ($this->source === 'jadwal') {
            $this->reply(
                "⚠️ Perintah tidak dikenali: <code>" . $this->notify->escape($commandText) . "</code>\nKirim <code>/jadwal</code> untuk daftar booking atau <code>/help</code> untuk bantuan.",
                $chatId,
                $messageId
            );

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

    protected function handleChatMigration(string $oldId, string $newId): void
    {
        $isJadwal = $this->source === 'jadwal';
        $ids = $isJadwal ? $this->jadwal->chatIds() : $this->notify->chatIds();
        $envKey = $isJadwal ? 'TELEGRAM_JADWAL_CHAT_ID' : 'TELEGRAM_CHAT_ID';

        if ($oldId === '' || $newId === '' || !in_array($oldId, $ids, true) || $oldId === $newId) {
            Log::info('Telegram: pesan migrasi chat diabaikan.', ['old' => $oldId, 'new' => $newId]);

            return;
        }

        $newIds = array_values(array_map(
            static fn ($id) => $id === $oldId ? $newId : $id,
            $ids
        ));

        if (!EnvFile::set([$envKey => implode(',', $newIds)])) {
            Log::warning('Telegram: gagal menyimpan chat ID hasil migrasi.');

            return;
        }

        Artisan::call('optimize:clear');
        Log::info('Telegram: chat ID dimigrasi otomatis.', ['old' => $oldId, 'new' => $newId, 'key' => $envKey]);

        $text = '✅ Chat ID diperbarui otomatis ke <code>' . $newId . '</code>. Notifikasi dan perintah aktif kembali.';

        if ($isJadwal) {
            $this->jadwal->sendToChat($newId, $text);
        } else {
            $this->notify->sendToChat($newId, $text);
        }
    }

    protected function handleJadwalCommand(string $text, string $chatId, int $messageId): void
    {
        $args = trim((string) preg_replace('/^\/[a-zA-Z]+(@[a-zA-Z0-9_]+)?/', '', $text));
        [$month, $error] = $this->parseMonthArg($args);

        if ($error !== null) {
            $this->reply('⚠️ ' . $error, $chatId, $messageId);

            return;
        }

        $sent = $this->jadwal->sendToChat(
            $chatId,
            $this->jadwal->message($month),
            $messageId ?: null,
            $this->jadwalToken()
        );

        if (!$sent) {
            $this->reply(
                '⚠️ Gagal mengirim jadwal: ' . $this->notify->escape((string) $this->jadwal->lastError()),
                $chatId,
                $messageId
            );
        }
    }

    protected function parseMonthArg(string $args): array
    {
        $args = trim($args);
        $tz = $this->jadwal->timezone();

        if ($args === '') {
            return [Carbon::now($tz), null];
        }

        if (preg_match('/^(0?[1-9]|1[0-2])(?:\s*[-\/]\s*|\s+)?(\d{4})?$/', $args, $m)) {
            $year = !empty($m[2]) ? (int) $m[2] : (int) Carbon::now($tz)->year;

            return [Carbon::create($year, (int) $m[1], 1, 0, 0, 0, $tz), null];
        }

        $months = [
            'januari' => 1, 'january' => 1, 'jan' => 1,
            'februari' => 2, 'february' => 2, 'feb' => 2,
            'maret' => 3, 'march' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'june' => 6, 'jun' => 6,
            'juli' => 7, 'july' => 7, 'jul' => 7,
            'agustus' => 8, 'august' => 8, 'agu' => 8, 'agt' => 8, 'aug' => 8,
            'september' => 9, 'sep' => 9, 'sept' => 9,
            'oktober' => 10, 'october' => 10, 'okt' => 10, 'oct' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'december' => 12, 'des' => 12, 'dec' => 12,
        ];

        if (preg_match('/^([a-zA-Z]+)(?:\s*[-\/]?\s*(\d{4}))?$/', $args, $m)) {
            $monthNumber = $months[mb_strtolower($m[1])] ?? null;

            if ($monthNumber !== null) {
                $year = !empty($m[2]) ? (int) $m[2] : (int) Carbon::now($tz)->year;

                return [Carbon::create($year, $monthNumber, 1, 0, 0, 0, $tz), null];
            }
        }

        return [null, 'Format bulan tidak dikenali. Contoh: <code>/jadwal</code>, <code>/jadwal 10</code>, <code>/jadwal Oktober 2026</code>.'];
    }

    protected function jadwalToken(): string
    {
        if ($this->source === 'jadwal') {
            return $this->jadwal->token();
        }

        return trim((string) env('TELEGRAM_BOT_TOKEN', ''));
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
        $invoice->amount = $amount;

        try {
            \DB::transaction(function () use ($booking, $invoice) {
                $booking->save();
                $invoice->save();
            });
        } catch (\Throwable $e) {
            Log::warning('Telegram bot: gagal update harga booking: ' . $e->getMessage());
            $this->reply('⚠️ Gagal menyimpan perubahan: ' . $this->notify->escape($e->getMessage()), $chatId, $messageId, $invoice);

            return;
        }

        try {
            $this->pdf->purge($invoice);
        } catch (\Throwable $e) {
            Log::warning('Telegram bot: gagal hapus cache PDF invoice: ' . $e->getMessage());
        }

        $this->reply(
            "✅ <b>Harga booking & invoice diperbarui</b>\n"
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
        if ($this->source === 'jadwal') {
            return $this->jadwal->sendToChat($chatId, $text, $messageId ?: null, $this->jadwalToken());
        }

        return $this->notify->sendMessage($text, $invoice, $booking, $messageId, $chatId);
    }

    protected function formatAmount(float $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }

    protected function helpText(): string
    {
        $lines = [
            '<b>Perintah Bot Booking</b>',
            '━━━━━━━━━━━━━━━',
            'Kirim salah satu:',
            '• <code>/jadwal</code> → daftar booking bulan ini',
            '• <code>/jadwal 10</code> → daftar booking bulan Oktober',
            '• <code>/jadwal Oktober 2026</code> → daftar booking bulan tertentu',
            '',
            '• <code>INV-20260929-00178</code> → info invoice',
            '• <code>INV-20260929-00178/1500000</code> → update harga booking',
            '• <code>INV-20260929-00178/download</code> → kirim PDF invoice',
            '',
            'Atau <b>reply</b> pesan notifikasi booking lalu kirim:',
            '• <code>info</code>',
            '• <code>1500000</code>',
            '• <code>download</code>',
        ];

        if ($this->source === 'jadwal') {
            $lines = array_slice($lines, 0, 6);
        }

        return implode("\n", $lines);
    }
}
