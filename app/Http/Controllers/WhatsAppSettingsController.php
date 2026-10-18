<?php

namespace App\Http\Controllers;

use App\Services\FonnteNotificationService;
use App\Support\EnvFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class WhatsAppSettingsController extends Controller
{
    public function __construct(protected FonnteNotificationService $fonnte)
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
            'WHATSAPP_ENABLED' => $this->envBool('WHATSAPP_ENABLED', false),
            'FONNTE_TOKEN' => $this->envString('FONNTE_TOKEN'),
            'FONNTE_TARGET' => $this->envString('FONNTE_TARGET'),
        ];

        $template = $this->fonnte->template();
        $placeholders = $this->fonnte->placeholders();
        $preview = $this->fonnte->preview();

        return view('settings.whatsapp', compact('settings', 'template', 'placeholders', 'preview'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'WHATSAPP_ENABLED' => ['required', 'in:true,false,1,0'],
            'FONNTE_TOKEN' => ['nullable', 'string', 'max:255'],
            'FONNTE_TARGET' => ['required', 'string', 'max:500'],
            'WHATSAPP_TEMPLATE' => ['required', 'string', 'max:4000'],
        ]);

        $template = (string) $data['WHATSAPP_TEMPLATE'];
        unset($data['WHATSAPP_TEMPLATE']);

        if (!$this->writeEnv($data)) {
            return back()->with('error', '.env tidak ditemukan atau tidak bisa ditulis.');
        }

        if (!$this->fonnte->saveTemplate($template)) {
            return back()->with('error', 'Template pesan gagal disimpan (folder storage tidak bisa ditulis).');
        }

        Artisan::call('optimize:clear');

        return back()->with('success', 'Pengaturan notifikasi WhatsApp berhasil disimpan.');
    }

    public function test(): RedirectResponse
    {
        if ($this->fonnte->sendTest()) {
            return back()->with('success', 'Pesan test berhasil dikirim ke WhatsApp.');
        }

        $error = (string) $this->fonnte->lastError();

        if (str_contains(strtolower($error), 'unauthorized') || str_contains(strtolower($error), 'invalid token')) {
            $error .= ' — pastikan Token Fonnte benar dan device sudah terhubung di dashboard Fonnte.';
        }

        return back()->with('error', 'Gagal mengirim pesan test: ' . ($error !== '' ? $error : 'unknown error'));
    }

    protected function writeEnv(array $data): bool
    {
        $unquotedKeys = ['WHATSAPP_ENABLED'];

        $normalized = [];
        foreach ($data as $key => $value) {
            $normalized[$key] = in_array($key, $unquotedKeys, true)
                ? (filter_var($value, FILTER_VALIDATE_BOOL) ? 'true' : 'false')
                : (string) $value;
        }

        return EnvFile::set($normalized, $unquotedKeys);
    }
}
