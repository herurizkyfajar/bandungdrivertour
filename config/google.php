<?php

return [
    'service_account_path' => storage_path(env('GOOGLE_SERVICE_ACCOUNT_PATH', 'app/google-service-account.json')),
    'spreadsheet_id' => env('GOOGLE_SPREADSHEET_ID'),
    'sheet_name' => 'Sheet1',
];
