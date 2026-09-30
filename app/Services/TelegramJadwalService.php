<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramJadwalService
{
    public const TEMPLATE_FILE = 'telegram-jadwal-message.txt';
    public const ITEM_TEMPLATE_FILE = 'telegram-jadwal-item.txt';

    public const DAYS = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    protected const MONTHS_ID = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    protected ?string $lastError = null;

    public function lastError(): ?string
    {
        return $this->lastError;
    }

    public function token(): string
    {
        $token = trim((string) env('TELEGRAM_JADWAL_BOT_TOKEN', ''));

        if ($token === '') {
            $token = trim((string) env('TELEGRAM_BOT_TOKEN', ''));
        }

        return $token;
    }

    public function chatIds(): array
    {
        return collect(explode(',', (string) env('TELEGRAM_JADWAL_CHAT_ID', '')))
            ->map(static fn ($v) => trim($v))
            ->filter()
            ->values()
            ->all();
    }

    public function enabled(): bool
    {
        return filter_var(env('TELEGRAM_JADWAL_ENABLED', false), FILTER_VALIDATE_BOOL)
            && $this->token() !== ''
            && $this->chatIds() !== [];
    }

    public function day(): int
    {
        $day = (int) env('TELEGRAM_JADWAL_DAY', 1);

        return ($day >= 1 && $day <= 7) ? $day : 1;
    }

    public function dayLabel(): string
    {
        return self::DAYS[$this->day()];
    }

    public function time(): string
    {
        $time = trim((string) env('TELEGRAM_JADWAL_TIME', '08:00'));

        return preg_match('/^\d{2}:\d{2}$/', $time) ? $time : '08:00';
    }

    public function timezone(): string
    {
        return 'Asia/Jakarta';
    }

    public function templatePath(): string
    {
        return storage_path('app/' . self::TEMPLATE_FILE);
    }

    public function itemTemplatePath(): string
    {
        return storage_path('app/' . self::ITEM_TEMPLATE_FILE);
    }

    public function defaultTemplate(): string
    {
        return implode("\n", [
            '<b>📅 JADWAL BOOKING — {{month}}</b>',
            'Periode {{period_start}} – {{period_end}}',
            'Total: <b>{{total}} booking</b>',
            '━━━━━━━━━━━━━━━',
            '{{list}}',
            '━━━━━━━━━━━━━━━',
            '🔁 Dikirim otomatis setiap {{day}} pukul {{time}} WIB',
        ]);
    }

    public function defaultItemTemplate(): string
    {
        return implode("\n", [
            '',
            '<b>{{no}}. {{customer_name}}</b>',
            '📅 {{date}} • {{time}} • 👥 {{passengers}} pax',
            '📍 {{pickup_location}}',
            '🚗 {{vehicle}}',
            '💰 {{price}} • 🏷 {{status}} • 🧾 {{invoice_number}}',
        ]);
    }

    public function template(): string
    {
        return $this->readTemplate($this->templatePath(), $this->defaultTemplate());
    }

    public function itemTemplate(): string
    {
        return $this->readTemplate($this->itemTemplatePath(), $this->defaultItemTemplate());
    }

    protected function readTemplate(string $path, string $default): string
    {
        if (is_file($path)) {
            $content = (string) file_get_contents($path);

            if (trim($content) !== '') {
                return $content;
            }
        }

        return $default;
    }

    public function saveTemplate(string $template): bool
    {
        return $this->writeTemplate($this->templatePath(), $template);
    }

    public function saveItemTemplate(string $template): bool
    {
        return $this->writeTemplate($this->itemTemplatePath(), $template);
    }

    protected function writeTemplate(string $path, string $template): bool
    {
        $dir = dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            return false;
        }

        return file_put_contents($path, $template) !== false;
    }

    public function placeholders(): array
    {
        $keys = [
            'month' => 'Nama bulan & tahun (SEPTEMBER 2026)',
            'period_start' => 'Tanggal awal periode (01/09/2026)',
            'period_end' => 'Tanggal akhir periode (30/09/2026)',
            'total' => 'Jumlah booking',
            'list' => 'Daftar booking (hasil template per booking)',
            'day' => 'Hari kirim (Senin)',
            'time' => 'Jam kirim (08:00)',
        ];

        return $this->toPlaceholderList($keys);
    }

    public function itemPlaceholders(): array
    {
        $keys = [
            'no' => 'Nomor urut',
            'customer_name' => 'Nama customer',
            'date' => 'Tanggal layanan (03/09 – 05/09)',
            'time' => 'Jam penjemputan (03.02)',
            'passengers' => 'Jumlah penumpang',
            'pickup_location' => 'Lokasi penjemputan',
            'vehicle' => 'Kendaraan',
            'price' => 'Harga (format ribuan)',
            'status' => 'Status booking',
            'invoice_number' => 'Nomor invoice',
            'booking_id' => 'ID booking',
        ];

        return $this->toPlaceholderList($keys);
    }

    protected function toPlaceholderList(array $keys): array
    {
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

    public function monthRange(?Carbon $month = null): array
    {
        $base = ($month ?? Carbon::now($this->timezone()))->copy();

        return [$base->copy()->startOfMonth(), $base->copy()->endOfMonth()];
    }

    public function bookings(?Carbon $month = null): Collection
    {
        [$start, $end] = $this->monthRange($month);

        return Booking::query()
            ->with(['vehicle', 'invoice'])
            ->whereDate('booking_date', '<=', $end->toDateString())
            ->whereRaw('COALESCE(end_date, booking_date) >= ?', [$start->toDateString()])
            ->orderBy('booking_date')
            ->orderBy('pickup_time')
            ->get();
    }

    public function message(?Carbon $month = null): string
    {
        [$start, $end] = $this->monthRange($month);
        $bookings = $this->bookings($month);

        $list = $bookings->isEmpty()
            ? 'Belum ada booking pada bulan ini.'
            : $bookings->map(fn (Booking $booking, int $index) => $this->renderItem($booking, $index + 1))
                ->implode("\n");

        $data = [
            'month' => strtoupper(self::MONTHS_ID[$start->month] . ' ' . $start->year),
            'period_start' => $start->format('d/m/Y'),
            'period_end' => $end->format('d/m/Y'),
            'total' => (string) $bookings->count(),
            'list' => $list,
            'day' => $this->dayLabel(),
            'time' => $this->time(),
        ];

        return trim($this->render($data, $this->template()));
    }

    protected function renderItem(Booking $booking, int $number): string
    {
        return $this->render($this->itemData($booking, $number), $this->itemTemplate());
    }

    protected function itemData(Booking $booking, int $number): array
    {
        $date = $booking->booking_date ? $booking->booking_date->format('d/m') : '-';
        if ($booking->end_date && $booking->booking_date && !$booking->end_date->equalTo($booking->booking_date)) {
            $date .= ' – ' . $booking->end_date->format('d/m');
        }

        return [
            'no' => (string) $number,
            'customer_name' => $this->escape((string) $booking->customer_name),
            'date' => $date,
            'time' => $booking->pickup_time ? $booking->pickup_time->format('H.i') : '-',
            'passengers' => (string) ($booking->number_of_passengers ?? '-'),
            'pickup_location' => $this->escape((string) $booking->pickup_location),
            'vehicle' => $booking->vehicle
                ? $this->escape(trim($booking->vehicle->make . ' ' . $booking->vehicle->model))
                : 'Tidak Ada',
            'price' => number_format((float) ($booking->price ?? 0), 0, ',', '.'),
            'status' => $this->escape($booking->statusLabel()),
            'invoice_number' => $this->escape((string) ($booking->invoice->invoice_number ?? '-')),
            'booking_id' => (string) $booking->id,
        ];
    }

    protected function render(array $data, string $template): string
    {
        foreach ($data as $key => $value) {
            $value = (string) $value;

            $template = str_replace(
                ['{{' . $key . '}}', '{{ ' . $key . ' }}'],
                $value,
                $template
            );
        }

        return $template;
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function send(?Carbon $month = null): bool
    {
        $this->lastError = null;

        if (!$this->enabled()) {
            $this->lastError = 'Pengaturan Tele Jadwal belum lengkap atau belum aktif.';

            return false;
        }

        $chunks = $this->chunk($this->message($month));
        $sent = false;
        $errors = [];

        foreach ($this->chatIds() as $chatId) {
            foreach ($chunks as $chunk) {
                if ($this->post($chunk, (string) $chatId)) {
                    $sent = true;
                } elseif ($this->lastError !== null) {
                    $errors[] = $this->lastError;
                }
            }
        }

        if (!$sent) {
            $this->lastError = implode('; ', array_unique($errors)) ?: 'Gagal mengirim pesan jadwal.';
        }

        return $sent;
    }

    public function sendToChat(string $chatId, string $text, ?int $replyToMessageId = null, ?string $token = null): bool
    {
        $this->lastError = null;

        $sent = false;
        $errors = [];

        foreach ($this->chunk($text) as $chunk) {
            if ($this->post($chunk, $chatId, $token, $replyToMessageId)) {
                $sent = true;
            } elseif ($this->lastError !== null) {
                $errors[] = $this->lastError;
            }
        }

        if (!$sent) {
            $this->lastError = implode('; ', array_unique($errors)) ?: 'Gagal mengirim pesan jadwal.';
        }

        return $sent;
    }

    protected function post(string $text, string $chatId, ?string $token = null, ?int $replyToMessageId = null): bool
    {
        $token = trim((string) ($token ?? ''));
        if ($token === '') {
            $token = $this->token();
        }

        if ($token === '') {
            $this->lastError = 'Bot token belum diisi.';

            return false;
        }

        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyToMessageId) {
            $payload['reply_to_message_id'] = $replyToMessageId;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Accept' => 'application/json'])
                ->post('https://api.telegram.org/bot' . $token . '/sendMessage', $payload);

            if ($response->failed() || !$response->json('ok')) {
                $this->lastError = $response->json('description') ?? ('HTTP ' . $response->status());
                Log::warning('Tele Jadwal gagal kirim ke ' . $chatId . ': ' . $this->lastError);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            Log::warning('Tele Jadwal gagal kirim ke ' . $chatId . ': ' . $e->getMessage());

            return false;
        }
    }

    protected function chunk(string $text, int $limit = 4000): array
    {
        if (mb_strlen($text) <= $limit) {
            return [$text];
        }

        $chunks = [];
        $current = '';

        foreach (explode("\n", $text) as $line) {
            while (mb_strlen($line) > $limit) {
                $chunks[] = mb_substr($line, 0, $limit);
                $line = mb_substr($line, $limit);
            }

            if ($current !== '' && mb_strlen($current) + mb_strlen($line) + 1 > $limit) {
                $chunks[] = $current;
                $current = '';
            }

            $current .= ($current === '' ? '' : "\n") . $line;
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }
}
