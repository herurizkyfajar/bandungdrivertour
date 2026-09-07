<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\InvoiceSetting;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\BatchUpdateValuesRequest;
use Google\Service\Sheets\ClearValuesRequest;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    protected ?Sheets $sheets = null;
    protected ?Client $client = null;
    protected string $spreadsheetId;
    protected string $sheetName;

    public function __construct()
    {
        $this->spreadsheetId = config('google.spreadsheet_id');
        $this->sheetName = config('google.sheet_name', 'Sheet1');
    }

    protected function getClient(): Client
    {
        if ($this->client) {
            return $this->client;
        }

        $client = new Client();
        $client->setApplicationName('BDT Rental Google Sheets');
        $client->setScopes([Sheets::SPREADSHEETS]);
        $client->setAuthConfig(config('google.service_account_path'));
        $client->setAccessType('offline');

        $this->client = $client;

        return $this->client;
    }

    protected function getSheetsService(): Sheets
    {
        if ($this->sheets) {
            return $this->sheets;
        }

        $this->sheets = new Sheets($this->getClient());

        return $this->sheets;
    }

    protected function getHeaderRow(): array
    {
        return [
            'No. Invoice',
            'Customer',
            'Asal Negara',
            'Status',
            'Tanggal Mulai',
            'Biaya',
            'Pendapatan',
            'Pajak',
        ];
    }

    protected function formatBookingRow(Booking $booking): array
    {
        $pajakRate = InvoiceSetting::instance()->pajak_rate ?? 0;
        $pendapatan = $booking->pendapatan ?? 0;
        $pajak = $pendapatan > 0 ? $pendapatan * $pajakRate / 100 : 0;

        return [
            $booking->invoice->invoice_number ?? '-',
            $booking->customer_name ?? '-',
            $booking->country_of_origin ?? '-',
            $booking->statusLabel(),
            $booking->booking_date ? $booking->booking_date->format('d M Y') : '-',
            $booking->price ? 'Rp ' . number_format($booking->price, 0, ',', '.') : '-',
            $pendapatan > 0 ? 'Rp ' . number_format($pendapatan, 0, ',', '.') : '-',
            $pajak > 0 ? 'Rp ' . number_format($pajak, 0, ',', '.') : '-',
        ];
    }

    public function ensureHeaderExists(): void
    {
        try {
            $service = $this->getSheetsService();
            $response = $service->spreadsheets_values->get($this->spreadsheetId, "{$this->sheetName}!A1:H1");
            $values = $response->getValues();

            if (empty($values) || $values[0] !== $this->getHeaderRow()) {
                $headerRange = "{$this->sheetName}!A1:H1";
                $body = new ValueRange(['values' => [$this->getHeaderRow()]]);
                $service->spreadsheets_values->update($this->spreadsheetId, $headerRange, $body, ['valueInputOption' => 'RAW']);
            }
        } catch (\Exception $e) {
            Log::error('Google Sheets: Failed to ensure header row', ['error' => $e->getMessage()]);
        }
    }

    public function syncAllData(): int
    {
        try {
            $service = $this->getSheetsService();

            $clearRange = "{$this->sheetName}!A2:H";
            $clearBody = new ClearValuesRequest();
            $service->spreadsheets_values->clear($this->spreadsheetId, $clearRange, $clearBody);

            $this->ensureHeaderExists();

            $pajakRate = InvoiceSetting::instance()->pajak_rate ?? 0;

            $bookings = Booking::with(['invoice'])
                ->withTrashed()
                ->orderBy('created_at', 'asc')
                ->get();

            if ($bookings->isEmpty()) {
                return 0;
            }

            $rows = $bookings->map(fn (Booking $b) => $this->formatBookingRow($b))->toArray();

            $dataRange = "{$this->sheetName}!A2:H" . (1 + count($rows));
            $body = new ValueRange(['values' => $rows]);
            $service->spreadsheets_values->update($this->spreadsheetId, $dataRange, $body, ['valueInputOption' => 'RAW']);

            return $bookings->count();
        } catch (\Exception $e) {
            Log::error('Google Sheets: Failed to sync all data', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function addRow(Booking $booking): void
    {
        try {
            $service = $this->getSheetsService();
            $this->ensureHeaderExists();

            $row = $this->formatBookingRow($booking);

            $range = "{$this->sheetName}!A:H";
            $body = new ValueRange(['values' => [$row]]);
            $service->spreadsheets_values->append($this->spreadsheetId, $range, $body, ['valueInputOption' => 'RAW', 'insertDataOption' => 'INSERT_ROWS']);
        } catch (\Exception $e) {
            Log::error('Google Sheets: Failed to add row', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    public function updateRow(Booking $booking): void
    {
        try {
            $service = $this->getSheetsService();

            $response = $service->spreadsheets_values->get($this->spreadsheetId, "{$this->sheetName}!A2:A");
            $values = $response->getValues() ?? [];

            $invoiceNumber = $booking->invoice->invoice_number ?? '-';
            $rowIndex = null;

            foreach ($values as $index => $row) {
                if (isset($row[0]) && $row[0] === $invoiceNumber) {
                    $rowIndex = $index + 2;
                    break;
                }
            }

            if ($rowIndex === null) {
                $this->addRow($booking);
                return;
            }

            $rowData = $this->formatBookingRow($booking);
            $range = "{$this->sheetName}!A{$rowIndex}:H{$rowIndex}";
            $body = new ValueRange(['values' => [$rowData]]);
            $service->spreadsheets_values->update($this->spreadsheetId, $range, $body, ['valueInputOption' => 'RAW']);
        } catch (\Exception $e) {
            Log::error('Google Sheets: Failed to update row', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    public function deleteRow(Booking $booking): void
    {
        try {
            $service = $this->getSheetsService();

            $response = $service->spreadsheets_values->get($this->spreadsheetId, "{$this->sheetName}!A2:A");
            $values = $response->getValues() ?? [];

            $invoiceNumber = $booking->invoice->invoice_number ?? '-';
            $rowIndex = null;

            foreach ($values as $index => $row) {
                if (isset($row[0]) && $row[0] === $invoiceNumber) {
                    $rowIndex = $index + 2;
                    break;
                }
            }

            if ($rowIndex === null) {
                return;
            }

            $requestBody = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                'requests' => [
                    [
                        'deleteDimension' => [
                            'range' => [
                                'sheetId' => $this->getSheetId(),
                                'startIndex' => $rowIndex - 1,
                                'endIndex' => $rowIndex,
                            ],
                        ],
                    ],
                ],
            ]);

            $service->spreadsheets_batchUpdate($this->spreadsheetId, $requestBody);
        } catch (\Exception $e) {
            Log::error('Google Sheets: Failed to delete row', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    protected function getSheetId(): int
    {
        $service = $this->getSheetsService();
        $spreadsheet = $service->spreadsheets->get($this->spreadsheetId);
        $sheets = $spreadsheet->getSheets();

        foreach ($sheets as $sheet) {
            if ($sheet->getProperties()->getTitle() === $this->sheetName) {
                return $sheet->getProperties()->getSheetId();
            }
        }

        return 0;
    }
}
