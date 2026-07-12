<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Itinerary;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewInvoiceNotificationMail;
use App\Models\EmailLog;


class BookingController extends Controller
{
    public function create()
    {
        $rawVehicles = Vehicle::select('id', 'make', 'model')->orderBy('make')->orderBy('model')->get();
        $vehicles = $rawVehicles->groupBy(function ($v) {
            return strtolower(trim($v->make)) . '|' . strtolower(trim($v->model));
        })->map(function ($group) {
            return $group->first();
        })->values();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $groups = Group::orderBy('name')->get();

        $itineraries = collect();
        if (Auth::check()) {
            $itineraries = Itinerary::where('user_id', Auth::id())
                ->where('status', 'active')
                ->orderBy('start_date', 'desc')
                ->get();
        }

        return view('booking.create', compact('vehicles', 'services', 'itineraries', 'groups'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:50'],
            'number_of_passengers' => ['nullable', 'integer', 'min:1'],
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'pickup_address_en' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'itinerary_id' => ['nullable', 'exists:itineraries,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'travel_plans' => ['nullable', 'string'],
            'info_source' => ['nullable', 'string'],
            'info_source_other' => ['nullable', 'string'],
            'payment_plan' => ['required', 'in:down_payment,payment_full_transfer,payment_full_on_driver'],
            'down_payment_amount' => ['nullable', 'numeric', 'min:0', 'required_if:payment_plan,down_payment'],
        ]);

        $bookingData = [
            'customer_name' => $data['customer_name'],
            'contact_number' => $data['contact_number'],
            'number_of_passengers' => $data['number_of_passengers'] ?? null,
            'country_of_origin' => $data['country_of_origin'] ?? null,
            'pickup_location' => $data['pickup_location'],
            'pickup_address_en' => $data['pickup_address_en'] ?? null,
            'booking_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'pickup_time' => $data['pickup_time'],
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'service_id' => $data['service_id'] ?? null,
            'itinerary_id' => $data['itinerary_id'] ?? null,
            'group_id' => $data['group_id'] ?? null,
            'travel_plans' => $data['travel_plans'] ?? null,
            'info_source' => $data['info_source'] ?? null,
            'info_source_other' => $data['info_source_other'] ?? null,
            'payment_plan' => $data['payment_plan'],
            'down_payment_amount' => $data['payment_plan'] === 'down_payment'
                ? ($data['down_payment_amount'] ?? null)
                : null,
            'created_by' => Auth::id(),
        ];

        $booking = Booking::create($bookingData);
        $invoiceNumber = $this->generateInvoiceNumber();
        $invoice = Invoice::create([
            'booking_id' => $booking->id,
            'invoice_number' => $invoiceNumber,
            'amount' => 0,
            'status' => 'unpaid',
            'issued_at' => now(),
        ]);

        // ==================== TAMBAHAN LOGIKA WEBHOOK N8N ====================
        try {
            // Memuat relasi kendaraan dan layanan jika dipilih oleh kustomer
            $booking->load(['vehicle', 'service']);

            $webhookData = [
                'customer_name'  => $booking->customer_name,
                'contact_number' => $booking->contact_number,
                'pickup_location'=> $booking->pickup_location,
                'booking_date'   => date('d-m-Y', strtotime($booking->booking_date)),
                'pickup_time'    => $booking->pickup_time,
                'vehicle_name'   => $booking->vehicle ? ($booking->vehicle->make . ' ' . $booking->vehicle->model) : 'Tidak Ada',
                'service_name'   => $booking->service ? $booking->service->name : 'Tidak Ada',
                'invoice_number' => $invoice->invoice_number,
                'booking_url'    => route('invoice.show', $invoice), // Tautan menuju detail invoice kustomer
            ];

            // Mengirim data ke n8n Production URL (Otomatis berjalan tanpa perlu diklik manual)
            Http::post('https://n8n-krkduh4jiei1.jkt6.sumopod.my.id/webhook/61bdb245-2610-4f6f-929c-281f2036b78e', $webhookData);
        } catch (\Throwable $e) {
            // Mencegah aplikasi utama crash apabila server n8n sedang overload atau lambat merespons
            Log::warning('Gagal mengirim data booking kustomer ke n8n: ' . $e->getMessage());
        }
        // =====================================================================

        app(WebPushService::class)->sendInvoiceCreated([
            'title' => 'Invoice Baru Masuk',
            'body' => $invoice->invoice_number . ' - ' . ($booking->customer_name ?? 'Customer'),
            'url' => route('invoice.show', $invoice),
            'invoice_id' => $invoice->id,
        ]);
        $notifyTo = (string) config('mail.invoice_notify_to', '');
        if ($notifyTo !== '') {
            $subject = 'Invoice Baru: ' . ($invoice->invoice_number ?? ('#' . $invoice->id)) . ' - ' . ($booking->customer_name ?? 'Customer');
            try {
                Mail::to($notifyTo)->send(new NewInvoiceNotificationMail($invoice));
                EmailLog::create([
                    'type' => 'invoice_notification',
                    'to_email' => $notifyTo,
                    'subject' => $subject,
                    'status' => 'sent',
                    'invoice_id' => $invoice->id,
                    'booking_id' => $booking->id,
                    'sent_at' => now(),
                ]);
            } catch (\Throwable $e) {
                EmailLog::create([
                    'type' => 'invoice_notification',
                    'to_email' => $notifyTo,
                    'subject' => $subject,
                    'status' => 'failed',
                    'invoice_id' => $invoice->id,
                    'booking_id' => $booking->id,
                    'error_message' => $e->getMessage(),
                ]);
                Log::warning('Failed to send new invoice email notification', [
                    'invoice_id' => $invoice->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }
        return redirect()->route('booking.create')->with('success', 'Your booking has been submitted successfully. Our admin will issue your invoice shortly via the WhatsApp number you provided.');
    }

    protected function generateInvoiceNumber(): string
    {
        $today = now()->format('Ymd');
        $prefix = 'INV-' . $today . '-';
        $sequenceRegex = '/^INV-\d{8}-(\d{1,5})(?:_|$)/';

        $nextSeq = 161;
        $latestInvoices = Invoice::orderByDesc('id')
            ->limit(500)
            ->get(['invoice_number']);

        foreach ($latestInvoices as $invoice) {
            if (preg_match($sequenceRegex, (string) $invoice->invoice_number, $m)) {
                $nextSeq = max(161, ((int) $m[1]) + 1);
                break;
            }
        }

        return $prefix . str_pad((string) $nextSeq, 5, '0', STR_PAD_LEFT);
    }
}