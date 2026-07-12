<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SmtpSettingsController extends Controller
{
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
            'MAIL_MAILER' => $this->envString('MAIL_MAILER', 'smtp'),
            'MAIL_HOST' => $this->envString('MAIL_HOST'),
            'MAIL_PORT' => $this->envString('MAIL_PORT'),
            'MAIL_USERNAME' => $this->envString('MAIL_USERNAME'),
            'MAIL_PASSWORD' => $this->envString('MAIL_PASSWORD'),
            'MAIL_ENCRYPTION' => $this->envString('MAIL_ENCRYPTION'),
            'MAIL_FROM_ADDRESS' => $this->envString('MAIL_FROM_ADDRESS'),
            'MAIL_FROM_NAME' => $this->envString('MAIL_FROM_NAME'),
            'INVOICE_NOTIFY_EMAIL' => $this->envString('INVOICE_NOTIFY_EMAIL'),
            'MAIL_VERIFY_PEER' => $this->envBool('MAIL_VERIFY_PEER', true),
            'MAIL_VERIFY_PEER_NAME' => $this->envBool('MAIL_VERIFY_PEER_NAME', true),
            'MAIL_ALLOW_SELF_SIGNED' => $this->envBool('MAIL_ALLOW_SELF_SIGNED', false),
        ];

        return view('settings.smtp', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'MAIL_MAILER' => ['required', 'string', 'max:50'],
            'MAIL_HOST' => ['required', 'string', 'max:255'],
            'MAIL_PORT' => ['required', 'integer', 'min:1', 'max:65535'],
            'MAIL_USERNAME' => ['required', 'string', 'max:255'],
            'MAIL_PASSWORD' => ['required', 'string', 'max:255'],
            'MAIL_ENCRYPTION' => ['nullable', 'string', 'max:20'],
            'MAIL_FROM_ADDRESS' => ['required', 'email', 'max:255'],
            'MAIL_FROM_NAME' => ['required', 'string', 'max:255'],
            'INVOICE_NOTIFY_EMAIL' => ['required', 'email', 'max:255'],
            'MAIL_VERIFY_PEER' => ['required', 'in:true,false,1,0'],
            'MAIL_VERIFY_PEER_NAME' => ['required', 'in:true,false,1,0'],
            'MAIL_ALLOW_SELF_SIGNED' => ['required', 'in:true,false,1,0'],
        ]);

        $mailUrl = sprintf(
            'smtp://%s:%s@%s:%d?verify_peer=%s&verify_peer_name=%s&allow_self_signed=%s',
            rawurlencode((string) $data['MAIL_USERNAME']),
            rawurlencode((string) $data['MAIL_PASSWORD']),
            (string) $data['MAIL_HOST'],
            (int) $data['MAIL_PORT'],
            filter_var($data['MAIL_VERIFY_PEER'], FILTER_VALIDATE_BOOL) ? '1' : '0',
            filter_var($data['MAIL_VERIFY_PEER_NAME'], FILTER_VALIDATE_BOOL) ? '1' : '0',
            filter_var($data['MAIL_ALLOW_SELF_SIGNED'], FILTER_VALIDATE_BOOL) ? '1' : '0',
        );
        $data['MAIL_URL'] = $mailUrl;

        $envPath = base_path('.env');
        if (!file_exists($envPath) || !is_writable($envPath)) {
            return back()->with('error', '.env tidak ditemukan atau tidak bisa ditulis.');
        }

        $env = file_get_contents($envPath);

        $unquotedKeys = ['MAIL_PORT', 'MAIL_VERIFY_PEER', 'MAIL_VERIFY_PEER_NAME', 'MAIL_ALLOW_SELF_SIGNED'];

        foreach ($data as $key => $value) {
            $normalizedValue = in_array($key, ['MAIL_VERIFY_PEER', 'MAIL_VERIFY_PEER_NAME', 'MAIL_ALLOW_SELF_SIGNED'], true)
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

        return back()->with('success', 'Pengaturan SMTP berhasil disimpan dan config sudah di-refresh.');
    }
}
