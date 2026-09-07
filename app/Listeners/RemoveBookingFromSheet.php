<?php

namespace App\Listeners;

use App\Events\BookingDeleted;
use App\Services\GoogleSheetsService;
use Illuminate\Support\Facades\Log;

class RemoveBookingFromSheet
{
    public function __construct(protected GoogleSheetsService $sheets)
    {
    }

    public function handle(BookingDeleted $event): void
    {
        try {
            $this->sheets->deleteRow($event->booking);
        } catch (\Exception $e) {
            Log::error('Google Sheets: RemoveBookingFromSheet failed', [
                'booking_id' => $event->booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
