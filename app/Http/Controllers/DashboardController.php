<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Mitra;
use App\Models\Invoice;
use App\Models\EmailLog;
use App\Mail\NewInvoiceNotificationMail;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user?->role ?? 'guest';

        if ($role === 'super_admin') {
            $metrics = [
                'bookings' => Booking::count(),
                'gross_revenue' => (float) Booking::sum('price'),
                'booking_pending' => Booking::whereIn('status', ['baru_masuk'])->count(),
                'booking_proses' => Booking::whereIn('status', ['konfirmasi', 'dijadwalkan'])->count(),
                'booking_selesai' => Booking::whereIn('status', ['selesai_pelayanan', 'selesai_administrasi_fee'])->count(),
                'booking_cancel' => Booking::whereIn('status', ['cancelled', 'cancel', 'batal'])->count(),
            ];
        } else {
            $userId = Auth::id();
            $metrics = [
                'my_bookings' => Booking::where('created_by', $userId)->count(),
                'pending_bookings' => Booking::where('created_by', $userId)->whereIn('status', ['baru_masuk', 'konfirmasi'])->count(),
                'completed_bookings' => Booking::where('created_by', $userId)->whereIn('status', ['selesai_pelayanan', 'selesai_administrasi_fee'])->count(),
            ];
        }

        return view('dashboard.index', compact('user', 'role', 'metrics'));
    }
    public function calendar()
    {
        $user = Auth::user();
        $role = $user?->role ?? 'guest';
        return view('dashboard.calendar', compact('user', 'role'));
    }

    public function sendTestEmail(): RedirectResponse
    {
        $notifyTo = (string) config('mail.invoice_notify_to', '');
        if ($notifyTo === '') {
            return back()->with('error', 'INVOICE_NOTIFY_EMAIL belum diatur di .env');
        }

        $invoice = Invoice::with(['booking.service', 'booking.vehicle', 'booking.mitra'])->latest()->first();
        if (!$invoice) {
            return back()->with('error', 'Belum ada invoice untuk dijadikan sample test email.');
        }

        try {
            Mail::to($notifyTo)->send(new NewInvoiceNotificationMail($invoice));
            EmailLog::create([
                'type' => 'test_email',
                'to_email' => $notifyTo,
                'subject' => 'Invoice Baru: ' . ($invoice->invoice_number ?? ('#' . $invoice->id)) . ' - ' . ($invoice->booking?->customer_name ?? 'Customer'),
                'status' => 'sent',
                'invoice_id' => $invoice->id,
                'booking_id' => $invoice->booking_id,
                'sent_at' => now(),
            ]);
            return back()->with('success', 'Test email berhasil dikirim ke ' . $notifyTo);
        } catch (\Throwable $e) {
            EmailLog::create([
                'type' => 'test_email',
                'to_email' => $notifyTo,
                'subject' => 'SMTP Test Email',
                'status' => 'failed',
                'invoice_id' => $invoice->id,
                'booking_id' => $invoice->booking_id,
                'error_message' => $e->getMessage(),
            ]);
            Log::warning('Failed to send SMTP test email', ['message' => $e->getMessage()]);
            return back()->with('error', 'Gagal kirim test email: ' . $e->getMessage());
        }
    }
}
