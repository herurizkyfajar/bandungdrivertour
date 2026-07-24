<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['vehicle', 'invoice'])
            ->withTrashed()
            ->orderBy('created_at', 'desc');

        if ($request->filled('filter_pendapatan') && $request->filter_pendapatan === 'kosong') {
            $query->whereNull('pendapatan')->orWhere('pendapatan', 0);
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $bookings = $query->paginate(25)->appends($request->query());

        $summaryQuery = Booking::withoutGlobalScopes();
        if ($request->filled('filter_pendapatan') && $request->filter_pendapatan === 'kosong') {
            $summaryQuery->where(function ($q) { $q->whereNull('pendapatan')->orWhere('pendapatan', 0); });
        }
        if ($request->filled('filter_status')) {
            $summaryQuery->where('status', $request->filter_status);
        }

        $totalBiaya = (clone $summaryQuery)->sum('price');
        $totalPendapatan = (clone $summaryQuery)->whereNotNull('pendapatan')->where('pendapatan', '>', 0)->sum('pendapatan');
        $belumDiisi = (clone $summaryQuery)->where(function ($q) { $q->whereNull('pendapatan')->orWhere('pendapatan', 0); })->count();

        return view('laporan-keuangan.index', compact('bookings', 'totalBiaya', 'totalPendapatan', 'belumDiisi'));
    }

    public function updatePendapatan(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $data = $request->validate([
            'pendapatan' => 'required|numeric|min:0',
        ]);

        $booking->update(['pendapatan' => $data['pendapatan']]);

        return redirect()->route('laporan-keuangan.index')
            ->with('success', 'Pendapatan berhasil diperbarui.');
    }
}
