<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
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

        $trendLabels = [];
        $trendValues = [];
        $trendRanges = [];

        if ($role === 'super_admin') {
            $metrics = [
                'bookings' => Booking::count(),
                'gross_revenue' => (float) Booking::sum('price'),
                'booking_masuk' => Booking::whereIn('status', ['baru_masuk', 'pending'])->count(),
                'booking_proses' => Booking::whereIn('status', ['konfirmasi', 'dijadwalkan'])->count(),
                'booking_selesai' => Booking::whereIn('status', ['selesai_pelayanan', 'selesai_administrasi_fee'])->count(),
                'booking_cancel' => Booking::whereIn('status', ['cancelled', 'cancel', 'batal'])->count(),
            ];

            // Pertumbuhan bulan berjalan dibanding bulan sebelumnya (hanya untuk tampilan).
            $thisMonth = [now()->startOfMonth(), now()->endOfMonth()];
            $lastMonth = [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()];

            $metrics['bookings_growth'] = $this->growthPercent(
                Booking::whereBetween('created_at', $thisMonth)->count(),
                Booking::whereBetween('created_at', $lastMonth)->count()
            );
            $metrics['revenue_growth'] = $this->growthPercent(
                (float) Booking::whereBetween('created_at', $thisMonth)->sum('price'),
                (float) Booking::whereBetween('created_at', $lastMonth)->sum('price')
            );

            // Data tren booking untuk semua pilihan filter pada grafik.
            $trendRanges = $this->trendRanges();
            $trendLabels = $trendRanges['7d']['labels'];
            $trendValues = $trendRanges['7d']['values'];
        } else {
            $userId = Auth::id();
            $metrics = [
                'my_bookings' => Booking::where('created_by', $userId)->count(),
                'pending_bookings' => Booking::where('created_by', $userId)->whereIn('status', ['baru_masuk', 'konfirmasi'])->count(),
                'completed_bookings' => Booking::where('created_by', $userId)->whereIn('status', ['selesai_pelayanan', 'selesai_administrasi_fee'])->count(),
            ];
        }

        return view('dashboard.index', compact('user', 'role', 'metrics', 'trendLabels', 'trendValues', 'trendRanges'));
    }

    /**
     * Jumlah booking per periode (berdasarkan tanggal pembuatan) untuk filter grafik tren.
     */
    private function trendRanges(): array
    {
        $created = Booking::query()->pluck('created_at')->map(fn ($value) => Carbon::parse($value));
        $countByDay = $created->countBy(fn (Carbon $date) => $date->toDateString());
        $countByMonth = $created->countBy(fn (Carbon $date) => $date->format('Y-m'));
        $countByYear = $created->countBy(fn (Carbon $date) => $date->format('Y'));

        // 7 hari terakhir.
        $labels7 = [];
        $values7 = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $labels7[] = $day->format('d M');
            $values7[] = (int) ($countByDay[$day->toDateString()] ?? 0);
        }

        // 1 minggu ini (Senin sampai Minggu).
        $weekStart = now()->startOfWeek(Carbon::MONDAY);
        $labelsWeek = [];
        $valuesWeek = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $labelsWeek[] = $day->format('d M');
            $valuesWeek[] = (int) ($countByDay[$day->toDateString()] ?? 0);
        }

        // 12 bulan terakhir.
        $labelsYear = [];
        $valuesYear = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->copy()->subMonths($i)->startOfMonth();
            $labelsYear[] = $month->format('M y');
            $valuesYear[] = (int) ($countByMonth[$month->format('Y-m')] ?? 0);
        }

        // Semua data, dikelompokkan per tahun.
        $labelsAll = [];
        $valuesAll = [];
        $minYear = (int) ($created->min(fn (Carbon $date) => (int) $date->format('Y')) ?? now()->format('Y'));
        $maxYear = (int) now()->format('Y');
        for ($year = $minYear; $year <= $maxYear; $year++) {
            $labelsAll[] = (string) $year;
            $valuesAll[] = (int) ($countByYear[(string) $year] ?? 0);
        }

        return [
            '7d' => [
                'labels' => $labels7,
                'values' => $values7,
                'subtitle' => 'Perkembangan jumlah booking 7 hari terakhir.',
            ],
            '1w' => [
                'labels' => $labelsWeek,
                'values' => $valuesWeek,
                'subtitle' => 'Jumlah booking minggu ini (Senin - Minggu).',
            ],
            '1y' => [
                'labels' => $labelsYear,
                'values' => $valuesYear,
                'subtitle' => 'Jumlah booking 12 bulan terakhir.',
            ],
            'all' => [
                'labels' => $labelsAll,
                'values' => $valuesAll,
                'subtitle' => 'Jumlah booking sejak data pertama.',
            ],
        ];
    }

    /**
     * Persentase perubahan antara periode berjalan dan periode sebelumnya.
     * Mengembalikan null bila periode sebelumnya tidak ada datanya.
     */
    private function growthPercent(float $current, float $previous): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
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
