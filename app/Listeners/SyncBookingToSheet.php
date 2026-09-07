<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Services\GoogleSheetsService;
use Illuminate\Support\Facades\Log;

class SyncBookingToSheet
{
    public function __construct(protected GoogleSheetsService $sheets)
    {
    }

    public function handle(BookingCreated $event): void
    {
        try {
            $this->sheets->addRow($event->booking);
        } catch (\Exception $e) {
            Log::error('Google Sheets: SyncBookingToSheet failed', [
                'booking_id' => $event->booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
