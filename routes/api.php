<?php

use Illuminate\Support\Facades\Route;
use App\Models\Booking;

Route::get('/bookings', function () {
    $bookings = Booking::with(['vehicle','service','mitra','invoice'])->latest()->take(100)->get();
    return response()->json($bookings);
});

Route::get('/bookings/{booking}', function (Booking $booking) {
    return response()->json($booking->load(['vehicle','service','mitra','invoice']));
});
