<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\InvoiceSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class GoogleSheetsService
{
    protected string $spreadsheetId;
    protected string $sheetName;
    protected string $serviceAccountEmail;
    protected ?string $privateKey = null;

    public function __construct()
    {
        $this->spreadsheetId = config('google.spreadsheet_id');
        $this->sheetName = config('google.sheet_name', 'Sheet1');

        $keyFile = config('google.service_account_path');
        if (file_exists($keyFile)) {
            $sa = json_decode(file_get_contents($keyFile), true);
            $this->serviceAccountEmail = $sa['client_email'] ?? '';
            $this->privateKey = $sa['private_key'] ?? '';
        }
    }

    protected function getAccessToken(): string
    {
        $now = time();
        $jwtPayload = [
            'iss' => $this->serviceAccountEmail,
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $jwt = JWT::encode($jwtPayload, $this->privateKey, 'RS256');

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        $data = $response->json();

        if (!$response->successful() || empty($data['access_token'])) {
            throw new \RuntimeException('Failed to get Google access token: ' . ($data['error_description'] ?? $data['error'] ?? 'Unknown error'));
        }

        return $data['access_token'];
    }

    protected function apiGet(string $endpoint, array $params = []): array
    {
        $token = $this->getAccessToken();
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$endpoint}";

        $response = Http::withToken($token)->get($url, $params);

        if (!$response->successful()) {
            throw new \RuntimeException('Google Sheets API GET error: ' . $response->body());
        }

        return $response->json();
    }

    protected function apiUpdate(string $endpoint, array $body, array $queryParams = []): array
    {
        $token = $this->getAccessToken();
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$endpoint}";

        $response = Http::withToken($token)
            ->withQueryParameters(array_merge($queryParams, ['valueInputOption' => 'RAW']))
            ->put($url, $body);

        if (!$response->successful()) {
            throw new \RuntimeException('Google Sheets API PUT error: ' . $response->body());
        }

        return $response->json();
    }

    protected function apiAppend(string $endpoint, array $body, array $queryParams = []): array
    {
        $token = $this->getAccessToken();
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$endpoint}:append";

        $response = Http::withToken($token)
            ->withQueryParameters(array_merge($queryParams, [
                'valueInputOption' => 'RAW',
                'insertDataOption' => 'INSERT_ROWS',
            ]))
            ->post($url, $body);

        if (!$response->successful()) {
            throw new \RuntimeException('Google Sheets API POST error: ' . $response->body());
        }

        return $response->json();
    }

    protected function apiClear(string $endpoint): void
    {
        $token = $this->getAccessToken();
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$endpoint}:clear";

        $response = Http::withToken($token)->post($url, (object) []);

        if (!$response->successful()) {
            throw new \RuntimeException('Google Sheets API CLEAR error: ' . $response->body());
        }
    }

    protected function apiBatchUpdate(array $requests): array
    {
        $token = $this->getAccessToken();
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}:batchUpdate";

        $response = Http::withToken($token)->post($url, [
            'requests' => $requests,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Google Sheets API batchUpdate error: ' . $response->body());
        }

        return $response->json();
    }

    protected function apiSpreadsheetGet(): array
    {
        $token = $this->getAccessToken();
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}";

        $response = Http::withToken($token)->get($url);

        if (!$response->successful()) {
            throw new \RuntimeException('Google Sheets API GET spreadsheet error: ' . $response->body());
        }

        return $response->json();
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
            $response = $this->apiGet("{$this->sheetName}!A1:H1");
            $values = $response['values'] ?? [];

            if (empty($values) || $values[0] !== $this->getHeaderRow()) {
                $this->apiUpdate("{$this->sheetName}!A1:H1", [
                    'values' => [$this->getHeaderRow()],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Google Sheets: Failed to ensure header row', ['error' => $e->getMessage()]);
        }
    }

    public function syncAllData(): int
    {
        try {
            $this->apiClear("{$this->sheetName}!A2:H");
            $this->ensureHeaderExists();

            $bookings = Booking::with(['invoice'])
                ->withTrashed()
                ->orderBy('created_at', 'asc')
                ->get();

            if ($bookings->isEmpty()) {
                return 0;
            }

            $rows = $bookings->map(fn (Booking $b) => $this->formatBookingRow($b))->toArray();

            $lastRow = 1 + count($rows);
            $this->apiUpdate("{$this->sheetName}!A2:H{$lastRow}", [
                'values' => $rows,
            ]);

            return $bookings->count();
        } catch (\Exception $e) {
            Log::error('Google Sheets: Failed to sync all data', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function addRow(Booking $booking): void
    {
        try {
            $this->ensureHeaderExists();
            $row = $this->formatBookingRow($booking);

            $this->apiAppend("{$this->sheetName}!A:H", [
                'values' => [$row],
            ]);
        } catch (\Exception $e) {
            Log::error('Google Sheets: Failed to add row', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    public function updateRow(Booking $booking): void
    {
        try {
            $response = $this->apiGet("{$this->sheetName}!A2:A");
            $values = $response['values'] ?? [];

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
            $this->apiUpdate("{$this->sheetName}!A{$rowIndex}:H{$rowIndex}", [
                'values' => [$rowData],
            ]);
        } catch (\Exception $e) {
            Log::error('Google Sheets: Failed to update row', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    public function deleteRow(Booking $booking): void
    {
        try {
            $response = $this->apiGet("{$this->sheetName}!A2:A");
            $values = $response['values'] ?? [];

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

            $sheetId = $this->getSheetId();
            $this->apiBatchUpdate([
                [
                    'deleteDimension' => [
                        'range' => [
                            'sheetId' => $sheetId,
                            'startIndex' => $rowIndex - 1,
                            'endIndex' => $rowIndex,
                        ],
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Google Sheets: Failed to delete row', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
        }
    }

    protected function getSheetId(): int
    {
        $spreadsheet = $this->apiSpreadsheetGet();
        $sheets = $spreadsheet['sheets'] ?? [];

        foreach ($sheets as $sheet) {
            if (($sheet['properties']['title'] ?? '') === $this->sheetName) {
                return (int) ($sheet['properties']['sheetId'] ?? 0);
            }
        }

        return 0;
    }
}
