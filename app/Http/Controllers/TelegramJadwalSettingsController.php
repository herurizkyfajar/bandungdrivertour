<?php

namespace App\Http\Controllers;

use App\Services\TelegramJadwalService;
use App\Support\EnvFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramJadwalSettingsController extends Controller
{
    public function __construct(protected TelegramJadwalService $jadwal)
    {
    }

    protected function envBool(string $key, bool $default): string
    {
        return filter_var(env($key, $default), FILTER_VALIDATE_BOOL) ? 'true' : 'false';
    }

    protected function envString(string $key, string $default = ''): string
    {
        return (string) env($key, $default);
    }

    public function index()
    {
        $settings = [
            'TELEGRAM_JADWAL_ENABLED' => $this->envBool('TELEGRAM_JADWAL_ENABLED', false),
            'TELEGRAM_JADWAL_BOT_TOKEN' => $this->envString('TELEGRAM_JADWAL_BOT_TOKEN'),
            'TELEGRAM_JADWAL_CHAT_ID' => $this->envString('TELEGRAM_JADWAL_CHAT_ID'),
            'TELEGRAM_JADWAL_DAY' => $this->envString('TELEGRAM_JADWAL_DAY', '1'),
            'TELEGRAM_JADWAL_TIME' => $this->envString('TELEGRAM_JADWAL_TIME', '08:00'),
        ];

        $days = TelegramJadwalService::DAYS;
        $template = $this->jadwal->template();
        $itemTemplate = $this->jadwal->itemTemplate();
        $placeholders = $this->jadwal->placeholders();
        $itemPlaceholders = $this->jadwal->itemPlaceholders();
        $preview = $this->jadwal->message();
        $active = $this->jadwal->enabled();
        $fallbackToken = $this->envString('TELEGRAM_BOT_TOKEN') !== '';
        $webhook = $this->webhookStatus();

        return view('settings.telegram_jadwal', compact(
            'settings',
            'days',
            'template',
            'itemTemplate',
            'placeholders',
            'itemPlaceholders',
            'preview',
            'active',
            'fallbackToken',
            'webhook'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'TELEGRAM_JADWAL_ENABLED' => ['required', 'in:true,false,1,0'],
            'TELEGRAM_JADWAL_BOT_TOKEN' => ['nullable', 'string', 'max:255'],
            'TELEGRAM_JADWAL_CHAT_ID' => ['nullable', 'string', 'max:500'],
            'TELEGRAM_JADWAL_DAY' => ['required', 'integer', 'between:1,7'],
            'TELEGRAM_JADWAL_TIME' => ['required', 'date_format:H:i'],
            'TELEGRAM_JADWAL_TEMPLATE' => ['required', 'string', 'max:4000'],
            'TELEGRAM_JADWAL_ITEM_TEMPLATE' => ['required', 'string', 'max:4000'],
        ]);

        $template = (string) $data['TELEGRAM_JADWAL_TEMPLATE'];
        $itemTemplate = (string) $data['TELEGRAM_JADWAL_ITEM_TEMPLATE'];
        unset($data['TELEGRAM_JADWAL_TEMPLATE'], $data['TELEGRAM_JADWAL_ITEM_TEMPLATE']);

        $unquotedKeys = ['TELEGRAM_JADWAL_ENABLED'];

        $normalized = [];
        foreach ($data as $key => $value) {
            $normalized[$key] = in_array($key, $unquotedKeys, true)
                ? (filter_var($value, FILTER_VALIDATE_BOOL) ? 'true' : 'false')
                : (string) $value;
        }

        if (!EnvFile::set($normalized, $unquotedKeys)) {
            return back()->with('error', '.env tidak ditemukan atau tidak bisa ditulis.');
        }

        if (!$this->jadwal->saveTemplate($template) || !$this->jadwal->saveItemTemplate($itemTemplate)) {
            return back()->with('error', 'Template pesan gagal disimpan (folder storage tidak bisa ditulis).');
        }

        Artisan::call('optimize:clear');

        return back()->with('success', 'Pengaturan Tele Jadwal berhasil disimpan.');
    }

    public function test(): RedirectResponse
    {
        if ($this->jadwal->send()) {
            return back()->with('success', 'Pesan jadwal (daftar booking bulan ini) berhasil dikirim ke Telegram.');
        }

        $error = (string) $this->jadwal->lastError();

        if (str_contains($error, 'upgraded to a supergroup')) {
            $error .= ' — grup sudah menjadi supergroup: ambil Chat ID baru (format -100...) lalu perbarui di halaman ini.';
        }

        return back()->with('error', 'Gagal mengirim pesan jadwal: ' . ($error !== '' ? $error : 'unknown error'));
    }

    public function webhookActivate(): RedirectResponse
    {
        $token = trim((string) env('TELEGRAM_JADWAL_BOT_TOKEN', ''));
        if ($token === '') {
            $token = trim((string) env('TELEGRAM_BOT_TOKEN', ''));
        }

        if ($token === '') {
            return back()->with('error', 'Isi Bot Token terlebih dahulu, lalu simpan.');
        }

        $secret = trim((string) env('TELEGRAM_WEBHOOK_SECRET', ''));
        if ($secret === '') {
            $secret = Str::random(32);
            if (!EnvFile::set(['TELEGRAM_WEBHOOK_SECRET' => $secret])) {
                return back()->with('error', 'Gagal menyimpan secret token ke .env.');
            }
            Artisan::call('optimize:clear');
        }

        $url = route('telegram.webhook.jadwal');

        try {
            $response = Http::timeout(15)->post('https://api.telegram.org/bot' . $token . '/setWebhook', [
                'url' => $url,
                'secret_token' => $secret,
                'allowed_updates' => ['message'],
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghubungi Telegram: ' . $e->getMessage());
        }

        if ($response->failed() || !$response->json('ok')) {
            return back()->with('error', 'Telegram menolak setWebhook: '
                . ($response->json('description') ?? ('HTTP ' . $response->status()))
                . ' — pastikan situs menggunakan HTTPS.');
        }

        return back()->with('success', 'Webhook bot Tele Jadwal aktif: ' . $url);
    }

    public function webhookDelete(): RedirectResponse
    {
        $token = trim((string) env('TELEGRAM_JADWAL_BOT_TOKEN', ''));
        if ($token === '') {
            $token = trim((string) env('TELEGRAM_BOT_TOKEN', ''));
        }

        if ($token === '') {
            return back()->with('error', 'Bot Token belum diisi.');
        }

        try {
            $response = Http::timeout(15)->post('https://api.telegram.org/bot' . $token . '/deleteWebhook');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghubungi Telegram: ' . $e->getMessage());
        }

        if ($response->failed() || !$response->json('ok')) {
            return back()->with('error', 'Gagal menghapus webhook: '
                . ($response->json('description') ?? ('HTTP ' . $response->status())));
        }

        return back()->with('success', 'Webhook bot Tele Jadwal dihapus.');
    }

    protected function webhookStatus(): array
    {
        $status = [
            'url' => null,
            'pending_update_count' => null,
            'last_error_message' => null,
            'has_error' => false,
        ];

        $token = trim((string) env('TELEGRAM_JADWAL_BOT_TOKEN', ''));
        if ($token === '') {
            $token = trim((string) env('TELEGRAM_BOT_TOKEN', ''));
        }

        if ($token === '') {
            return $status;
        }

        try {
            $response = Http::timeout(5)->get('https://api.telegram.org/bot' . $token . '/getWebhookInfo');
            if ($response->failed() || !$response->json('ok')) {
                $status['last_error_message'] = $response->json('description') ?? ('HTTP ' . $response->status());
                $status['has_error'] = true;

                return $status;
            }

            $result = $response->json('result', []);
            $status['url'] = $result['url'] ?? null;
            $status['pending_update_count'] = $result['pending_update_count'] ?? null;
            $status['last_error_message'] = $result['last_error_message'] ?? '';
            $status['has_error'] = ($result['last_error_message'] ?? '') !== '';
        } catch (\Throwable $e) {
            $status['last_error_message'] = $e->getMessage();
            $status['has_error'] = true;
            Log::warning('Gagal mengambil status webhook Tele Jadwal: ' . $e->getMessage());
        }

        return $status;
    }
}
