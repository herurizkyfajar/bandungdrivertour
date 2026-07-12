<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class NotificationSoundSettingsController extends Controller
{
    protected function envBool(string $key, bool $default): string
    {
        return filter_var(env($key, $default), FILTER_VALIDATE_BOOL) ? 'true' : 'false';
    }

    protected function envString(string $key, string $default = ''): string
    {
        return (string) env($key, $default);
    }

    protected function envInt(string $key, int $default): int
    {
        return (int) env($key, $default);
    }

    public function index()
    {
        $settings = [
            'NOTIFY_SOUND_ENABLED' => $this->envBool('NOTIFY_SOUND_ENABLED', true),
            'NOTIFY_SOUND_TYPE' => $this->envString('NOTIFY_SOUND_TYPE', 'beep'),
            'NOTIFY_SOUND_VOLUME' => $this->envInt('NOTIFY_SOUND_VOLUME', 80),
            'NOTIFY_SOUND_REPEAT' => $this->envInt('NOTIFY_SOUND_REPEAT', 3),
            'NOTIFY_SOUND_INTERVAL_MS' => $this->envInt('NOTIFY_SOUND_INTERVAL_MS', 140),
        ];

        return view('settings.notification_sound', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'NOTIFY_SOUND_ENABLED' => ['required', 'in:true,false,1,0'],
            'NOTIFY_SOUND_TYPE' => ['required', 'in:beep,chime,bell,soft'],
            'NOTIFY_SOUND_VOLUME' => ['required', 'integer', 'min:1', 'max:100'],
            'NOTIFY_SOUND_REPEAT' => ['required', 'integer', 'min:1', 'max:5'],
            'NOTIFY_SOUND_INTERVAL_MS' => ['required', 'integer', 'min:50', 'max:1000'],
        ]);

        $envPath = base_path('.env');
        if (!file_exists($envPath) || !is_writable($envPath)) {
            return back()->with('error', '.env tidak ditemukan atau tidak bisa ditulis.');
        }

        $env = file_get_contents($envPath);
        $unquotedKeys = ['NOTIFY_SOUND_ENABLED', 'NOTIFY_SOUND_VOLUME', 'NOTIFY_SOUND_REPEAT', 'NOTIFY_SOUND_INTERVAL_MS'];

        foreach ($data as $key => $value) {
            $normalizedValue = $key === 'NOTIFY_SOUND_ENABLED'
                ? (filter_var($value, FILTER_VALIDATE_BOOL) ? 'true' : 'false')
                : (string) $value;

            $line = in_array($key, $unquotedKeys, true)
                ? $key . '=' . $normalizedValue
                : $key . '="' . str_replace('"', '\\"', $normalizedValue) . '"';

            if (preg_match('/^' . preg_quote($key, '/') . '=.*/m', $env)) {
                $env = preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $line, $env);
            } else {
                $env .= PHP_EOL . $line;
            }
        }

        file_put_contents($envPath, $env);
        Artisan::call('optimize:clear');

        return back()->with('success', 'Pengaturan suara notifikasi berhasil disimpan.');
    }
}
