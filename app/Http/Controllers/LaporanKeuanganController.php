<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Group;
use App\Models\InvoiceSetting;
use Illuminate\Http\Request;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'created_at');
        $dir = $request->input('dir', 'desc');
        $allowed = ['invoice_number', 'customer_name', 'country_of_origin', 'status', 'booking_date', 'price', 'pendapatan', 'created_at'];
        if (!in_array($sort, $allowed)) $sort = 'created_at';
        $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';

        $query = Booking::with(['vehicle', 'invoice'])
            ->withTrashed();

        if ($sort === 'invoice_number') {
            $query->join('invoices', 'invoices.booking_id', '=', 'bookings.id')
                  ->orderBy('invoices.invoice_number', $dir)
                  ->select('bookings.*');
        } elseif ($sort === 'booking_date') {
            $query->orderBy('booking_date', $dir);
        } else {
            $query->orderBy($sort, $dir);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qr) use ($q) {
                $qr->where('customer_name', 'like', "%{$q}%")
                   ->orWhere('contact_number', 'like', "%{$q}%")
                   ->orWhereHas('invoice', function ($iq) use ($q) {
                       $iq->where('invoice_number', 'like', "%{$q}%");
                   });
            });
        }

        if ($request->filled('filter_pendapatan') && $request->filter_pendapatan === 'kosong') {
            $query->whereNull('pendapatan')->orWhere('pendapatan', 0);
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        if ($request->filled('filter_group')) {
            $query->where('group_id', $request->filter_group);
        }

        if ($request->filled('filter_country')) {
            $query->where('country_of_origin', $request->filter_country);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->end_date);
        }

        $bookings = $query->paginate(25)->appends($request->query());

        $summaryQuery = Booking::withoutGlobalScopes();
        if ($request->filled('q')) {
            $q = $request->q;
            $summaryQuery->where(function ($qr) use ($q) {
                $qr->where('customer_name', 'like', "%{$q}%")
                   ->orWhere('contact_number', 'like', "%{$q}%")
                   ->orWhereHas('invoice', function ($iq) use ($q) {
                       $iq->where('invoice_number', 'like', "%{$q}%");
                   });
            });
        }
        if ($request->filled('filter_pendapatan') && $request->filter_pendapatan === 'kosong') {
            $summaryQuery->where(function ($q) { $q->whereNull('pendapatan')->orWhere('pendapatan', 0); });
        }
        if ($request->filled('filter_status')) {
            $summaryQuery->where('status', $request->filter_status);
        }
        if ($request->filled('filter_group')) {
            $summaryQuery->where('group_id', $request->filter_group);
        }
        if ($request->filled('filter_country')) {
            $summaryQuery->where('country_of_origin', $request->filter_country);
        }
        if ($request->filled('start_date')) {
            $summaryQuery->whereDate('booking_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $summaryQuery->whereDate('booking_date', '<=', $request->end_date);
        }

        $totalBiaya = (clone $summaryQuery)->sum('price');
        $totalPendapatan = (clone $summaryQuery)->whereNotNull('pendapatan')->where('pendapatan', '>', 0)->sum('pendapatan');
        $belumDiisi = (clone $summaryQuery)->where(function ($q) { $q->whereNull('pendapatan')->orWhere('pendapatan', 0); })->count();

        $groups = Group::orderBy('name')->get();
        $countries = Booking::whereNotNull('country_of_origin')->where('country_of_origin', '!=', '')->distinct()->pluck('country_of_origin')->sort()->values();

        $pajakRate = InvoiceSetting::instance()->pajak_rate ?? 0;
        $totalPajak = $totalPendapatan * ($pajakRate / 100);

        return view('laporan-keuangan.index', compact('bookings', 'totalBiaya', 'totalPendapatan', 'belumDiisi', 'groups', 'countries', 'pajakRate', 'totalPajak'));
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

    public function updatePajak(Request $request)
    {
        $data = $request->validate([
            'pajak_rate' => 'required|numeric|min:0|max:100',
        ]);

        $setting = InvoiceSetting::instance();
        $setting->update(['pajak_rate' => $data['pajak_rate']]);

        return redirect()->route('laporan-keuangan.index')
            ->with('success', 'Pajak berhasil diperbarui.');
    }
}
