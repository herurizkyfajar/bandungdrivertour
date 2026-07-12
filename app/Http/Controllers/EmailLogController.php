<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $status = trim((string) $request->query('status', ''));
        $q = trim((string) $request->query('q', ''));
        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 50, 100], true) ? $perPage : 10;

        $query = EmailLog::query()->latest();

        if (in_array($status, ['sent', 'failed'], true)) {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('to_email', 'like', '%' . $q . '%')
                  ->orWhere('subject', 'like', '%' . $q . '%')
                  ->orWhere('error_message', 'like', '%' . $q . '%');
            });
        }

        try {
            if ($dateFrom !== '' && $dateTo !== '') {
                $from = Carbon::parse($dateFrom)->startOfDay();
                $to = Carbon::parse($dateTo)->endOfDay();
                if ($from->lessThanOrEqualTo($to)) {
                    $query->whereBetween('created_at', [$from, $to]);
                }
            } elseif ($dateFrom !== '') {
                $from = Carbon::parse($dateFrom)->startOfDay();
                $query->where('created_at', '>=', $from);
            } elseif ($dateTo !== '') {
                $to = Carbon::parse($dateTo)->endOfDay();
                $query->where('created_at', '<=', $to);
            }
        } catch (\Throwable $e) {
            // Ignore invalid date filters and fall back to unfiltered dates.
        }

        $filteredQuery = clone $query;
        $stats = [
            'total' => (clone $filteredQuery)->count(),
            'sent' => (clone $filteredQuery)->where('status', 'sent')->count(),
            'failed' => (clone $filteredQuery)->where('status', 'failed')->count(),
        ];

        $emailLogs = $query->paginate($perPage)->withQueryString();

        return view('email_logs.index', compact('emailLogs', 'stats', 'perPage', 'status', 'q', 'dateFrom', 'dateTo'));
    }
}
