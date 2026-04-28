<?php

return [
    // 'public_key' => env('LICENSE_PUBLIC_KEY'),
    // 'public_key_path' => env('LICENSE_PUBLIC_KEY_PATH', storage_path('keys/public.pem')),
    'public_key' => file_get_contents(storage_path('keys/public.pem')),
    'aes_key' => env('LICENSE_AES_KEY'),
];
