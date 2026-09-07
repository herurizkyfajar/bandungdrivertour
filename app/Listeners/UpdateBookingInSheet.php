<?php

namespace App\Listeners;

use App\Events\BookingUpdated;
use App\Services\GoogleSheetsService;
use Illuminate\Support\Facades\Log;

class UpdateBookingInSheet
{
    public function __construct(protected GoogleSheetsService $sheets)
    {
    }

    public function handle(BookingUpdated $event): void
    {
        try {
            $this->sheets->updateRow($event->booking);
        } catch (\Exception $e) {
            Log::error('Google Sheets: UpdateBookingInSheet failed', [
                'booking_id' => $event->booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
