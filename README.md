# Vending Machine Mini

Aplikasi ini adalah sistem vending machine berbasis Laravel dengan pembayaran QRIS Midtrans dan integrasi hardware dispenser via Flask + pyserial.

## Ringkasan Sistem

- Frontend landing page untuk pilih produk dan checkout QRIS.
- Backend Laravel untuk transaksi, sinkronisasi status Midtrans, dan pengurangan stok.
- Service Python (`app.py`) untuk komunikasi serial ke motor controller vending.
- Saat pembayaran `paid`, stok diproses di Laravel, lalu command dispense hardware dikirim best-effort.

## Arsitektur Singkat

1. User checkout di web.
2. Laravel membuat transaksi `pending` + QRIS Midtrans.
3. Status pembayaran diupdate via webhook Midtrans atau polling Midtrans.
4. Jika status Midtrans `capture/settlement`, status internal jadi `paid`.
5. Laravel finalize transaksi (update `sales_lines`, kurangi stok, update `dispense_status`).
6. Setelah response selesai, Laravel trigger pemanggilan endpoint Flask `/dispense` per item (best-effort, tanpa menunggu ACK controller).

## Requirement

- PHP 8.2+
- Composer
- Node.js + npm
- Database (MySQL/SQLite sesuai env)
- Python 3.10+
- Python package: `flask`, `pyserial`

## Setup Laravel

1. Install dependency:

```bash
composer install
npm install
```

2. Buat env:

```bash
cp .env.example .env
php artisan key:generate
```

3. Konfigurasi database di `.env`, lalu migrate:

```bash
php artisan migrate
```

4. Jalankan aplikasi:

```bash
php artisan serve
npm run dev
```

## Konfigurasi Environment Penting

```env
APP_URL=http://localhost:8000

MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_SANITIZE=true
MIDTRANS_3DS=true
MIDTRANS_NOTIFICATION_URL=

VENDING_DISPENSE_URL=http://127.0.0.1:9000/dispense
VENDING_DISPENSE_TIMEOUT_SECONDS=0.25

CALL_CENTER_PHONE=0812-0000-0000
CALL_CENTER_WHATSAPP=0812-0000-0000
```

Setelah ubah `.env`:

```bash
php artisan config:clear
php artisan cache:clear
```

## Midtrans Integration

### Endpoint Transaksi

- `POST /api/transaction/checkout`
- `GET /api/transaction/status/{id}`
- `POST /api/transaction/cancel/{id}`

### Endpoint Webhook Midtrans

- `POST /api/webhooks/midtrans`
- Kompatibel lama: `POST /api/transaction/notify`

### Mapping Status Midtrans ke Status Internal

- `capture`, `settlement` -> `paid`
- `pending` -> `pending`
- `expire` -> `expired`
- `cancel`, `deny` -> `failed`

Catatan:
- Signature Midtrans diverifikasi.
- Status yang sudah `paid` tidak diturunkan lagi oleh notifikasi terlambat.

## Mode Update Status Pembayaran

### Opsi A (Direkomendasikan): Webhook publik

Set Notification URL Midtrans ke:

`POST https://your-domain.com/api/webhooks/midtrans`

### Opsi B: Polling Midtrans

Jalankan command:

```bash
php artisan transactions:sync-pending --limit=50
```

Untuk mode berkelanjutan:

```bash
php artisan schedule:work
```

## Integrasi Hardware Flask + pyserial

File service hardware: `app.py`

Endpoint lokal:

- `POST http://127.0.0.1:9000/dispense`
- `GET http://127.0.0.1:9000/health`

Contoh payload dispense:

```json
{
  "transaction_id": "ORDER123",
  "cell_id": "A"
}
```

Contoh jalankan service Python:

```bash
pip install flask pyserial
python app.py
```

Konfigurasi serial di `app.py`:

- `SERIAL_PORT` (contoh `COM5`)
- `BAUDRATE`
- `MOTOR_SPIN_SECONDS`

Penting:
- Saat ini sistem tidak mengandalkan response detail dari motor controller untuk menentukan sukses/gagal per item.
- Laravel mengirim command dispense sebagai best-effort setelah transaksi dipastikan `paid`.

## Alur UX Landing Page

- Landing page polling status pembayaran setiap beberapa detik.
- Begitu transaksi `paid`, UI menampilkan sukses dan stok produk langsung ter-update sesuai transaksi.

## Troubleshooting Cepat

- Pembayaran lama berubah status:
  - pastikan webhook Midtrans aktif, atau scheduler polling berjalan.
- Signature Midtrans invalid:
  - cek `MIDTRANS_SERVER_KEY` sesuai mode sandbox/production.
- Dispense tidak terpanggil:
  - pastikan Flask service hidup di `127.0.0.1:9000`.
  - cek `VENDING_DISPENSE_URL` di `.env`.
- Hardware tidak gerak:
  - cek `SERIAL_PORT`, baudrate, kabel, dan izin akses serial.
