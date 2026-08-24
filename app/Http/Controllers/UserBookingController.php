<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('created_by', Auth::id())
            ->with('vehicle', 'service', 'services', 'invoice')
            ->latest()
            ->paginate(10);

        return view('user.bookings', compact('bookings'));
    }

    public function destroy(Booking $booking)
    {
        if ($booking->created_by !== Auth::id()) {
            abort(403);
        }

        $booking->delete();

        return redirect()->route('user.bookings.history')->with('success', 'Booking deleted successfully.');
    }
}
