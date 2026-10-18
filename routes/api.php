<?php

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use App\Models\Booking;
use App\Http\Controllers\TelegramWebhookController;

Route::post('/webhook/telegram', [TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');

Route::post('/webhook/telegram-jadwal', [TelegramWebhookController::class, 'handleJadwal'])
    ->name('telegram.webhook.jadwal');

Route::get('/bookings', function (Request $request) {
    $query = Booking::with(['vehicle','service','mitra','invoice']);

    $start = $request->query('start');
    $end = $request->query('end');

    if ($start && $end) {
        $startDay = Carbon::parse($start)->toDateString();
        $endDay = Carbon::parse($end)->subDay()->toDateString();

        $query->where('booking_date', '<=', $endDay)
            ->where(function ($q) use ($startDay) {
                $q->where(function ($q2) use ($startDay) {
                    $q2->whereNull('end_date')->where('booking_date', '>=', $startDay);
                })->orWhere(function ($q2) use ($startDay) {
                    $q2->whereNotNull('end_date')->where('end_date', '>=', $startDay);
                });
            });
    }

    $bookings = $query->latest()->take(1000)->get();

    return response()->json($bookings);
});

Route::get('/bookings/stats', function () {
    $today = now()->toDateString();

    return response()->json([
        'total' => Booking::count(),
        'today' => Booking::whereDate('booking_date', $today)->count(),
        'upcoming' => Booking::whereDate('booking_date', '>=', $today)->count(),
    ]);
});

Route::get('/bookings/{booking}', function (Booking $booking) {
    return response()->json($booking->load(['vehicle','service','mitra','invoice']));
});
