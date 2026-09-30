<?php

namespace App\Http\Controllers;

use App\Services\TelegramNotificationService;
use App\Support\EnvFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramSettingsController extends Controller
{
    public function __construct(protected TelegramNotificationService $telegram)
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
            'TELEGRAM_ENABLED' => $this->envBool('TELEGRAM_ENABLED', false),
            'TELEGRAM_BOT_TOKEN' => $this->envString('TELEGRAM_BOT_TOKEN'),
            'TELEGRAM_CHAT_ID' => $this->envString('TELEGRAM_CHAT_ID'),
            'TELEGRAM_PARSE_MODE' => $this->envString('TELEGRAM_PARSE_MODE', 'HTML'),
            'TELEGRAM_COMMANDS_ENABLED' => $this->envBool('TELEGRAM_COMMANDS_ENABLED', true),
        ];

        $template = $this->telegram->template();
        $placeholders = $this->telegram->placeholders();
        $preview = $this->telegram->preview();
        $webhook = $this->webhookStatus();

        return view('settings.telegram', compact('settings', 'template', 'placeholders', 'preview', 'webhook'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'TELEGRAM_ENABLED' => ['required', 'in:true,false,1,0'],
            'TELEGRAM_BOT_TOKEN' => ['nullable', 'string', 'max:255'],
            'TELEGRAM_CHAT_ID' => ['required', 'string', 'max:500'],
            'TELEGRAM_PARSE_MODE' => ['required', 'in:HTML,None'],
            'TELEGRAM_COMMANDS_ENABLED' => ['required', 'in:true,false,1,0'],
            'TELEGRAM_TEMPLATE' => ['required', 'string', 'max:4000'],
        ]);

        $template = (string) $data['TELEGRAM_TEMPLATE'];
        unset($data['TELEGRAM_TEMPLATE']);

        if (!$this->writeEnv($data)) {
            return back()->with('error', '.env tidak ditemukan atau tidak bisa ditulis.');
        }

        if (!$this->telegram->saveTemplate($template)) {
            return back()->with('error', 'Template pesan gagal disimpan (folder storage tidak bisa ditulis).');
        }

        Artisan::call('optimize:clear');

        return back()->with('success', 'Pengaturan notifikasi Telegram berhasil disimpan.');
    }

    public function test(): RedirectResponse
    {
        if ($this->telegram->sendTest()) {
            return back()->with('success', 'Pesan test berhasil dikirim ke Telegram.');
        }

        $error = (string) $this->telegram->lastError();

        if (str_contains($error, 'upgraded to a supergroup')) {
            $error .= ' — grup sudah menjadi supergroup: ambil Chat ID baru (format -100...) dari getUpdates lalu perbarui di halaman ini.';
        }

        return back()->with('error', 'Gagal mengirim pesan test: ' . ($error !== '' ? $error : 'unknown error'));
    }

    public function webhookActivate(): RedirectResponse
    {
        $token = trim((string) env('TELEGRAM_BOT_TOKEN', ''));
        if ($token === '') {
            return back()->with('error', 'Isi Bot Token terlebih dahulu, lalu simpan.');
        }

        $secret = trim((string) env('TELEGRAM_WEBHOOK_SECRET', ''));
        if ($secret === '') {
            $secret = Str::random(32);
            if (!$this->writeEnv(['TELEGRAM_WEBHOOK_SECRET' => $secret])) {
                return back()->with('error', 'Gagal menyimpan secret token ke .env.');
            }
            Artisan::call('optimize:clear');
        }

        $url = route('telegram.webhook');

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

        return back()->with('success', 'Webhook aktif: ' . $url);
    }

    public function webhookDelete(): RedirectResponse
    {
        $token = trim((string) env('TELEGRAM_BOT_TOKEN', ''));
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

        return back()->with('success', 'Webhook dihapus.');
    }

    protected function webhookStatus(): array
    {
        $status = [
            'url' => null,
            'pending_update_count' => null,
            'last_error_message' => null,
            'has_error' => false,
        ];

        $token = trim((string) env('TELEGRAM_BOT_TOKEN', ''));
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
            Log::warning('Gagal mengambil status webhook Telegram: ' . $e->getMessage());
        }

        return $status;
    }

    protected function writeEnv(array $data): bool
    {
        $unquotedKeys = ['TELEGRAM_ENABLED', 'TELEGRAM_COMMANDS_ENABLED'];

        $normalized = [];
        foreach ($data as $key => $value) {
            $normalized[$key] = in_array($key, $unquotedKeys, true)
                ? (filter_var($value, FILTER_VALIDATE_BOOL) ? 'true' : 'false')
                : (string) $value;
        }

        return EnvFile::set($normalized, $unquotedKeys);
    }
}
