<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Vehicle;
use App\Models\Mitra;
use App\Models\User;
use App\Models\Service;
use App\Models\Itinerary;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewInvoiceNotificationMail;
use App\Models\EmailLog;

class BookingsController extends Controller
{
    public function index(Request $request)
    {
        $hasMitra = $request->query('has_mitra');
        $phase = $request->query('phase');
        $q = trim((string) $request->query('q', ''));

        $query = Booking::with(['vehicle','service','mitra','invoice'])->latest();

        if ($hasMitra === 'yes') {
            $query->whereNotNull('mitra_id');
        } elseif ($hasMitra === 'no') {
            $query->whereNull('mitra_id');
        }

        $allowedPhases = array_keys(Booking::KANBAN_PHASES);
        if (in_array($phase, $allowedPhases, true)) {
            $query->whereIn('status', Booking::phaseStatuses($phase));
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('customer_name', 'like', '%' . $q . '%')
                  ->orWhere('contact_number', 'like', '%' . $q . '%')
                  ->orWhere('country_of_origin', 'like', '%' . $q . '%')
                  ->orWhere('pickup_location', 'like', '%' . $q . '%')
                  ->orWhereHas('vehicle', function ($v) use ($q) {
                      $v->where('make', 'like', '%' . $q . '%')
                        ->orWhere('model', 'like', '%' . $q . '%');
                  })
                  ->orWhereHas('invoice', function ($inv) use ($q) {
                      $inv->where('invoice_number', 'like', '%' . $q . '%');
                  });
            });
        }

        $bookings = $query->orderByDesc('booking_date')
            ->orderByDesc('pickup_time')
            ->orderByDesc('id')
            ->get();

        $bookingsByPhase = $bookings->groupBy(fn (Booking $booking) => $booking->kanbanPhase());

        $counts = collect(Booking::KANBAN_PHASES)->mapWithKeys(function ($definition, $phaseKey) use ($bookingsByPhase) {
            return [$phaseKey => $bookingsByPhase->get($phaseKey, collect())->count()];
        });

        $filters = [
            'has_mitra' => $hasMitra,
            'phase' => $phase,
            'q' => $q,
        ];

        $notificationSoundSettings = [
            'enabled' => filter_var(env('NOTIFY_SOUND_ENABLED', true), FILTER_VALIDATE_BOOL),
            'type' => (string) env('NOTIFY_SOUND_TYPE', 'beep'),
            'volume' => (int) env('NOTIFY_SOUND_VOLUME', 80),
            'repeat' => (int) env('NOTIFY_SOUND_REPEAT', 3),
            'interval_ms' => (int) env('NOTIFY_SOUND_INTERVAL_MS', 140),
        ];

        return view('bookings.index', [
            'bookings' => $bookings,
            'bookingsByPhase' => $bookingsByPhase,
            'counts' => $counts,
            'filters' => $filters,
            'notificationSoundSettings' => $notificationSoundSettings,
        ]);
    }

    public function create()
    {
        $raw = Vehicle::select('id', 'make', 'model', 'sort_order')->orderBy('sort_order')->get();
        $vehicles = $raw->groupBy(function ($v) {
            return strtolower(trim($v->make)) . '|' . strtolower(trim($v->model));
        })->map(function ($group) {
            return $group->first();
        })->values();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $itineraries = Itinerary::with('user')->orderBy('start_date', 'desc')->get();
        $groups = Group::orderBy('name')->get();
        return view('bookings.create', compact('vehicles', 'services', 'itineraries', 'groups'));
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
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);
        $price = (Auth::user()?->role === 'super_admin') ? ($data['price'] ?? null) : null;
        $booking = Booking::create([
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
            'price' => $price,
            'status' => 'baru_masuk',
            'created_by' => Auth::id(),
        ]);
        $invoiceNumber = $this->generateInvoiceNumber();

        $invoice = Invoice::create([
            'booking_id' => $booking->id,
            'invoice_number' => $invoiceNumber,
            'amount' => (float) ($price ?? 0),
            'status' => 'unpaid',
            'issued_at' => now(),
        ]);

        // ==================== TAMBAHAN LOGIKA WEBHOOK N8N ====================
        try {
            // Memuat relasi kendaraan dan layanan jika dipilih oleh admin
            $booking->load(['vehicle', 'service']);

            $webhookData = [
                'customer_name'  => $booking->customer_name,
                'contact_number' => $booking->contact_number,
                'pickup_location'=> $booking->pickup_location,
                'booking_date'   => date('d-m-Y', strtotime($booking->booking_date)),
                'pickup_time'    => $booking->pickup_time,
                'vehicle_name'   => $booking->vehicle ? ($booking->vehicle->make . ' ' . $booking->vehicle->model) : 'Tidak Ada',
                'service_name'   => $booking->service ? $booking->service->name : 'Tidak Ada',
                'price'          => number_format($booking->price ?? 0, 0, ',', '.'),
                'invoice_number' => $invoice->invoice_number,
                'booking_url'    => route('invoice.show', $invoice), // Tautan menuju detail invoice
            ];

            // Mengirim data ke n8n Production URL (Otomatis berjalan tanpa perlu diklik manual)
            Http::post('https://n8n-krkduh4jiei1.jkt6.sumopod.my.id/webhook/61bdb245-2610-4f6f-929c-281f2036b78e', $webhookData);
        } catch (\Throwable $e) {
            // Mencegah aplikasi utama crash apabila server n8n sedang overload atau lambat merespons
            Log::warning('Gagal mengirim data booking baru (Admin) ke n8n: ' . $e->getMessage());
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
        return redirect()->route('bookings.index')->with('success', 'Booking created.');
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

    public function edit(Booking $booking)
    {
        $raw = Vehicle::select('id', 'make', 'model', 'mitra_id', 'sort_order')->orderBy('sort_order')->get();
        $vehicles = $raw->groupBy(function ($v) {
            return strtolower(trim($v->make)) . '|' . strtolower(trim($v->model));
        })->map(function ($group) {
            return $group->first();
        })->values();
        $mitraUserIds = User::where('role', 'mitra')->pluck('id');
        $mitraEmails = User::where('role', 'mitra')->pluck('email');
        $mitras = Mitra::whereIn('email', $mitraEmails)->orderBy('full_name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $itineraries = Itinerary::with('user')->orderBy('start_date', 'desc')->get();
        $groups = Group::orderBy('name')->get();
        return view('bookings.edit', compact('booking','vehicles','mitras','services','itineraries','groups'));
    }

    public function update(Request $request, Booking $booking)
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
            'mitra_id' => ['nullable', 'exists:mitras,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'travel_plans' => ['nullable', 'string'],
            'info_source' => ['nullable', 'string'],
            'info_source_other' => ['nullable', 'string'],
            'payment_plan' => ['required', 'in:down_payment,payment_full_transfer,payment_full_on_driver'],
            'down_payment_amount' => ['nullable', 'numeric', 'min:0', 'required_if:payment_plan,down_payment'],
            'status' => ['nullable', 'in:baru_masuk,konfirmasi,dijadwalkan,cancelled,cancel,batal,selesai_pelayanan,selesai_administrasi_fee'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'manual_invoice_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);
        $isSuperAdmin = (Auth::user()?->role === 'super_admin');
        $booking->customer_name = $data['customer_name'];
        $booking->contact_number = $data['contact_number'];
        $booking->number_of_passengers = $data['number_of_passengers'] ?? null;
        $booking->country_of_origin = $data['country_of_origin'] ?? null;
        $booking->pickup_location = $data['pickup_location'];
        $booking->pickup_address_en = $data['pickup_address_en'] ?? null;
        $booking->booking_date = $data['start_date'];
        $booking->end_date = $data['end_date'];
        $booking->pickup_time = $data['pickup_time'];
        $booking->vehicle_id = $data['vehicle_id'] ?? null;
        $booking->service_id = $data['service_id'] ?? null;
        $booking->itinerary_id = $data['itinerary_id'] ?? null;
        $booking->mitra_id = $data['mitra_id'] ?? null;
        $booking->group_id = $data['group_id'] ?? null;
        $booking->travel_plans = $data['travel_plans'] ?? null;
        $booking->info_source = $data['info_source'] ?? null;
        $booking->info_source_other = $data['info_source_other'] ?? null;
        $booking->payment_plan = $data['payment_plan'];
        $booking->down_payment_amount = $data['payment_plan'] === 'down_payment'
            ? ($data['down_payment_amount'] ?? null)
            : null;
        if ($isSuperAdmin) {
            $booking->price = $data['price'] ?? null;
        }
        if (isset($data['status'])) $booking->status = $data['status'];
        $booking->save();
        $invoice = $booking->invoice;
        if ($invoice) {
            $invoice->amount = (float) ($booking->price ?? 0);

            if ($isSuperAdmin && $request->hasFile('manual_invoice_file')) {
                if (!empty($invoice->manual_invoice_path) && Storage::disk('public')->exists($invoice->manual_invoice_path)) {
                    Storage::disk('public')->delete($invoice->manual_invoice_path);
                }

                $invoice->manual_invoice_path = $request->file('manual_invoice_file')->store('invoices/manual', 'public');
            }

            $invoice->save();
        }
        return redirect()->route('bookings.index')->with('success', 'Booking updated.');
    }

    public function updatePhase(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'phase' => ['required', Rule::in(array_keys(Booking::KANBAN_PHASES))],
        ]);

        $phase = $data['phase'];
        $phaseStatuses = Booking::phaseStatuses($phase);

        if (empty($phaseStatuses)) {
            return response()->json(['message' => 'Phase tidak valid.'], 422);
        }

        $booking->status = $phaseStatuses[0];
        $booking->save();

        return response()->json([
            'message' => 'Status booking diperbarui.',
            'phase' => $booking->kanbanPhase(),
            'status' => $booking->status,
            'status_label' => $booking->statusLabel(),
            'phase_label' => $booking->kanbanPhaseLabel(),
        ]);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking dipindahkan ke trash.');
    }

    public function trash()
    {
        $bookings = Booking::onlyTrashed()
            ->with(['vehicle', 'service', 'mitra', 'invoice'])
            ->latest('deleted_at')
            ->paginate(20);

        return view('bookings.trash', compact('bookings'));
    }

    public function restore($id)
    {
        $booking = Booking::onlyTrashed()->findOrFail($id);
        $booking->restore();
        return redirect()->route('bookings.trash')->with('success', 'Booking berhasil di-restore.');
    }

    public function forceDelete($id)
    {
        $booking = Booking::onlyTrashed()->findOrFail($id);
        Invoice::where('booking_id', $booking->id)->delete();
        $booking->forceDelete();
        return redirect()->route('bookings.trash')->with('success', 'Booking dihapus permanen.');
    }
}