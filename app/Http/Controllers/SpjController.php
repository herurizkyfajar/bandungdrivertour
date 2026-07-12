<?php

namespace App\Http\Controllers;

use App\Models\Booking;

class SpjController extends Controller
{
    public function show(Booking $booking)
    {
        return view('spj.show', compact('booking'));
    }
}
