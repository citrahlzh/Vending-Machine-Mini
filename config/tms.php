<?php

return [
    'base_url' => env('TMS_BASE_URL'),
    'machine_code' => env('TMS_MACHINE_CODE'),
    'api_key' => env('TMS_API_KEY'),
    'heartbeat_interval' => env('TMS_HEARTBEAT_INTERVAL', 60),
    'license_path' => env('TMS_LICENSE_PATH', storage_path('license/license.dat')),
    'license_grace_days' => env('TMS_LICENSE_GRACE_DAYS', 3),
    'push_retry_max' => env('TMS_PUSH_RETRY_MAX', 5),
    'transaction_push_mode' => env('TMS_TRANSACTION_PUSH_MODE', 'daily'),
];
