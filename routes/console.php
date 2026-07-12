<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('webpush:vapid', function () {
    $config = [
        'curve_name' => 'prime256v1',
        'private_key_type' => OPENSSL_KEYTYPE_EC,
    ];
    $res = openssl_pkey_new($config);
    if ($res === false) {
        $this->error('Failed to generate VAPID keys.');
        return;
    }

    openssl_pkey_export($res, $privatePem);
    $details = openssl_pkey_get_details($res);
    $ec = $details['ec'] ?? null;
    if (!$ec || empty($ec['x']) || empty($ec['y']) || empty($ec['d'])) {
        $this->error('Invalid EC key details.');
        return;
    }

    $publicRaw = "\x04" . $ec['x'] . $ec['y'];
    $privateRaw = $ec['d'];

    $b64 = function (string $bin): string {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    };

    $this->line('Add these to your .env file:');
    $this->line('VAPID_PUBLIC_KEY=' . $b64($publicRaw));
    $this->line('VAPID_PRIVATE_KEY=' . $b64($privateRaw));
    $this->line('VAPID_SUBJECT=mailto:admin@example.com');
})->purpose('Generate VAPID keys for web push notifications');
