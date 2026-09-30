<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(protected TelegramBotService $bot)
    {
    }

    public function handle(Request $request)
    {
        return $this->process($request, 'main');
    }

    public function handleJadwal(Request $request)
    {
        return $this->process($request, 'jadwal');
    }

    protected function process(Request $request, string $source)
    {
        $secret = trim((string) env('TELEGRAM_WEBHOOK_SECRET', ''));
        $provided = (string) $request->header('X-Telegram-Bot-Api-Secret-Token', '');

        if ($secret === '' || $provided === '' || !hash_equals($secret, $provided)) {
            Log::warning('Telegram webhook ditolak: secret token tidak cocok.', [
                'ip' => $request->ip(),
                'source' => $source,
            ]);

            return response()->json(['ok' => false, 'error' => 'unauthorized'], 403);
        }

        $update = $request->all();

        try {
            $this->bot->handleUpdate($update, $source);
        } catch (\Throwable $e) {
            Log::error('Telegram webhook gagal diproses: ' . $e->getMessage(), [
                'update' => array_keys($update),
                'source' => $source,
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
