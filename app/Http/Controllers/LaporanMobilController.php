<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanMobilController extends Controller
{
    private const CANCEL_STATUSES = ['cancelled', 'cancel', 'batal'];

    private const PERIODS = ['all', 'day', 'week', 'month', 'year', 'custom'];

    public function index(Request $request)
    {
        $period = $request->input('period', 'all');
        if (!in_array($period, self::PERIODS, true)) {
            $period = 'all';
        }

        [$rangeStart, $rangeEnd, $rangeLabel] = $this->resolveRange($period, $request);

        $statusFilter = (string) $request->input('status_filter', '');

        $query = Booking::query()->whereNotNull('vehicle_id');

        if ($statusFilter === 'all') {
            // tanpa filter status
        } elseif ($statusFilter === '') {
            $query->whereNotIn('status', self::CANCEL_STATUSES);
        } else {
            $query->where('status', $statusFilter);
        }

        if ($rangeStart && $rangeEnd) {
            $query->whereDate('booking_date', '<=', $rangeEnd->toDateString())
                ->whereRaw('COALESCE(end_date, booking_date) >= ?', [$rangeStart->toDateString()]);
        } elseif ($rangeStart) {
            $query->whereRaw('COALESCE(end_date, booking_date) >= ?', [$rangeStart->toDateString()]);
        } elseif ($rangeEnd) {
            $query->whereDate('booking_date', '<=', $rangeEnd->toDateString());
        }

        $bookings = $query->get(['id', 'vehicle_id', 'booking_date', 'end_date']);

        $usage = [];
        foreach ($bookings as $booking) {
            $start = $booking->booking_date->copy();
            $end = ($booking->end_date && $booking->end_date->greaterThanOrEqualTo($start))
                ? $booking->end_date->copy()
                : $start->copy();

            if ($rangeStart && $start->lessThan($rangeStart)) {
                $start = $rangeStart->copy();
            }
            if ($rangeEnd && $end->greaterThan($rangeEnd)) {
                $end = $rangeEnd->copy();
            }
            if ($start->greaterThan($end)) {
                continue;
            }

            $vehicleId = $booking->vehicle_id;
            if (!isset($usage[$vehicleId])) {
                $usage[$vehicleId] = ['count' => 0, 'days' => 0, 'bookings' => []];
            }

            $usage[$vehicleId]['count']++;
            $usage[$vehicleId]['days'] += (int) round($start->startOfDay()->diffInDays($end->startOfDay())) + 1;
            $usage[$vehicleId]['bookings'][] = $booking->id;
        }

        $vehicles = Vehicle::query()->get();

        $rows = $vehicles->map(function (Vehicle $vehicle) use ($usage) {
            $data = $usage[$vehicle->id] ?? ['count' => 0, 'days' => 0, 'bookings' => []];

            $name = trim($vehicle->make . ' ' . $vehicle->model);
            if ($name === '') {
                $name = $vehicle->plate_number;
            }

            return [
                'vehicle' => $vehicle,
                'name' => $name,
                'count' => $data['count'],
                'days' => $data['days'],
            ];
        });

        $rows = $rows->sort(function ($a, $b) {
            $cmp = $b['count'] <=> $a['count'];
            if ($cmp === 0) {
                $cmp = $b['days'] <=> $a['days'];
            }
            if ($cmp === 0) {
                $cmp = strcmp($a['name'], $b['name']);
            }

            return $cmp;
        })->values();

        $totalBookings = (int) $rows->sum('count');
        $totalDays = (int) $rows->sum('days');
        $vehiclesUsed = (int) $rows->filter(fn ($row) => $row['count'] > 0)->count();
        $totalVehicles = $rows->count();

        $rangeDays = null;
        if ($rangeStart && $rangeEnd) {
            $rangeDays = (int) round($rangeStart->startOfDay()->diffInDays($rangeEnd->startOfDay())) + 1;
        }

        $chartData = $rows
            ->filter(fn ($row) => $row['count'] > 0)
            ->take(10)
            ->map(fn ($row) => [
                'label' => $row['name'],
                'count' => $row['count'],
                'days' => $row['days'],
            ])
            ->values();

        $statuses = [];
        foreach (Booking::KANBAN_PHASES as $phase) {
            foreach ($phase['statuses'] as $status) {
                $statuses[$status] = ucfirst(str_replace('_', ' ', $status));
            }
        }

        return view('laporan-mobil.index', [
            'rows' => $rows,
            'period' => $period,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'rangeLabel' => $rangeLabel,
            'rangeDays' => $rangeDays,
            'statusFilter' => $statusFilter,
            'statuses' => $statuses,
            'totalBookings' => $totalBookings,
            'totalDays' => $totalDays,
            'vehiclesUsed' => $vehiclesUsed,
            'totalVehicles' => $totalVehicles,
            'chartData' => $chartData,
        ]);
    }

    private function resolveRange(string $period, Request $request): array
    {
        $today = Carbon::today();

        if ($period === 'day') {
            return [$today->copy(), $today->copy(), $today->format('d M Y')];
        }

        if ($period === 'week') {
            $start = $today->copy()->startOfWeek();
            $end = $today->copy()->endOfWeek();

            return [$start, $end, $start->format('d M Y') . ' - ' . $end->format('d M Y')];
        }

        if ($period === 'month') {
            $start = $today->copy()->startOfMonth();
            $end = $today->copy()->endOfMonth();

            return [$start, $end, $start->format('d M Y') . ' - ' . $end->format('d M Y')];
        }

        if ($period === 'year') {
            $start = $today->copy()->startOfYear();
            $end = $today->copy()->endOfYear();

            return [$start, $end, $start->format('d M Y') . ' - ' . $end->format('d M Y')];
        }

        if ($period === 'custom') {
            $start = $this->parseDate($request->input('start_date'));
            $end = $this->parseDate($request->input('end_date'));

            if ($start && $end && $start->greaterThan($end)) {
                [$start, $end] = [$end, $start];
            }

            if ($start && $end) {
                $label = $start->format('d M Y') . ' - ' . $end->format('d M Y');
            } elseif ($start) {
                $label = 'Sejak ' . $start->format('d M Y');
            } elseif ($end) {
                $label = 'Sampai ' . $end->format('d M Y');
            } else {
                $label = 'Semua tanggal';
            }

            return [$start, $end, $label];
        }

        return [null, null, 'Semua data'];
    }

    private function parseDate(?string $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
