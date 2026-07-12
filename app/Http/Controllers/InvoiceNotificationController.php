<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;

class InvoiceNotificationController extends Controller
{
    public function latest(): JsonResponse
    {
        $latest = Invoice::with('booking:id,customer_name')
            ->latest('id')
            ->first(['id', 'booking_id', 'invoice_number', 'amount', 'created_at']);

        if (!$latest) {
            return response()->json([
                'latest_id' => null,
            ]);
        }

        return response()->json([
            'latest_id' => $latest->id,
            'invoice_number' => $latest->invoice_number,
            'customer_name' => $latest->booking?->customer_name,
            'amount' => (float) $latest->amount,
            'created_at' => optional($latest->created_at)->toIso8601String(),
        ]);
    }
}
