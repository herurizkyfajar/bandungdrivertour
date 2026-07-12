<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Group;

class BookingDataController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['invoice', 'vehicle', 'mitra', 'service', 'group'])
            ->latest()
            ->get();

        $groups = Group::orderBy('name')->get();

        $rawBookings = $bookings->map(function($b) {
            $hasInvoice = $b->invoice;
            $whatsappNumber = preg_replace('/[^0-9]/', '', $b->contact_number ?? '');
            return [
                'id' => $b->id,
                'invoice_number' => $hasInvoice?->invoice_number ?? '-',
                'customer_name' => $b->customer_name ?? '-',
                'amount' => $hasInvoice?->amount ?? 0,
                'vehicle' => $b->vehicle ? trim($b->vehicle->make . ' ' . $b->vehicle->model) : '-',
                'mitra' => $b->mitra?->full_name ?? '-',
                'status' => $b->status ?? '',
                'status_label' => $b->statusLabel(),
                'contact_number' => $b->contact_number ?? '-',
                'number_of_passengers' => $b->number_of_passengers ?? '-',
                'country_of_origin' => $b->country_of_origin ?? '-',
                'pickup_location' => $b->pickup_location ?? '-',
                'pickup_address_en' => $b->pickup_address_en ?? '-',
                'service_name' => $b->service?->name ?? '-',
                'payment_plan' => $b->payment_plan ?? '-',
                'down_payment_amount' => $b->down_payment_amount ? 'Rp ' . number_format((float) $b->down_payment_amount, 0, ',', '.') : '-',
                'price' => $b->price ? number_format((float) $b->price, 0, ',', '.') : '0',
                'booking_date' => $b->booking_date?->format('d M Y') ?? '-',
                'booking_date_iso' => $b->booking_date?->format('Y-m-d') ?? null,
                'end_date' => $b->end_date?->format('d M Y') ?? '-',
                'travel_plans' => $b->travel_plans ?? '',
                'group_name' => $b->group?->name ?? null,
                'info_source' => $b->info_source ?? null,
                'invoice_show_url' => $hasInvoice ? route('invoice.show', $hasInvoice) : null,
                'invoice_download_url' => $hasInvoice ? route('invoice.show', $hasInvoice) . '?download=1' : null,
                'edit_url' => route('bookings.edit', $b->id),
                'whatsapp_url' => $whatsappNumber !== '' ? 'https://wa.me/' . $whatsappNumber : null,
            ];
        });

        return view('booking-data.index', [
            'rawBookings' => $rawBookings,
            'groups' => $groups,
        ]);
    }
}
