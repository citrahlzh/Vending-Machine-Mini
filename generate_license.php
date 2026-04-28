<?php

$licenseData = [
    "app" => "NEXSELL Vending Machine",
    "machine_code" => "VM-AAA",
    "usb_serial" => "0101021ea2c26d839659",
    "expired" => "2026-04-30"
];

$data = json_encode($licenseData);

// === LOAD PRIVATE KEY ===
$privateKeyPath = __DIR__ . '/private.pem';
if (!file_exists($privateKeyPath)) {
    die("Private key tidak ditemukan!");
}

$privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));
if (!$privateKey) {
    die("Private key tidak valid!");
}

// === SIGN ===
if (!openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
    die("Gagal signing!");
}

$license = [
    "data" => $licenseData,
    "signature" => base64_encode($signature)
];

// === ENCRYPT (AES) ===
$key = "CHANGE_THIS_SECRET_KEY_32BYTE"; // nanti pindah ke .env di Laravel
$iv = random_bytes(16);

$encrypted = openssl_encrypt(
    json_encode($license),
    'AES-256-CBC',
    $key,
    0,
    $iv
);

if (!$encrypted) {
    die("Gagal enkripsi!");
}

// gabung IV + data
file_put_contents("license.dat", base64_encode($iv . $encrypted));

echo "License berhasil dibuat (SIGNED + ENCRYPTED)\n";
