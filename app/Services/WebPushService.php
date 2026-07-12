<?php

namespace App\Services;

use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;

class WebPushService
{
    public function sendInvoiceCreated(array $payload): void
    {
        if (!class_exists('Minishlink\\WebPush\\WebPush')) {
            Log::warning('WebPush dependency not installed. Skipping push send.');
            return;
        }

        $publicKey = (string) config('services.webpush.public_key');
        $privateKey = (string) config('services.webpush.private_key');
        $subject = (string) config('services.webpush.subject');

        if ($publicKey === '' || $privateKey === '' || $subject === '') {
            Log::warning('VAPID config missing. Skipping push send.');
            return;
        }

        $auth = [
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ];

        $webPush = new \Minishlink\WebPush\WebPush($auth);
        $subs = PushSubscription::query()->get();

        foreach ($subs as $sub) {
            $subscription = \Minishlink\WebPush\Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
                'contentEncoding' => $sub->content_encoding ?: 'aesgcm',
            ]);

            $webPush->queueNotification($subscription, json_encode($payload));
        }

        foreach ($webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                Log::warning('Push failed to send', ['endpoint' => $endpoint, 'reason' => $report->getReason()]);
            }
        }
    }
}
