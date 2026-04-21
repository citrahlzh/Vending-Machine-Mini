# Vending Machine Mini

Aplikasi ini adalah sistem vending machine berbasis Laravel dengan pembayaran QRIS Midtrans, integrasi hardware dispenser via Flask + pyserial, histori audit log, dan dukungan update status payment dari webhook, polling, maupun notifikasi MQTT.

## Ringkasan Sistem

- Frontend landing page untuk pilih produk, checkout, dan permainan promosi.
- Backend Laravel untuk transaksi, sinkronisasi payment Midtrans, pengurangan stok, reward game, dan audit log aktivitas aplikasi.
- Service Python (`app.py`) untuk komunikasi serial ke motor controller vending.
- Saat payment `paid`, Laravel finalize transaksi lalu mengirim command dispense hardware secara best-effort.

## Fitur Utama

- Checkout QRIS Midtrans.
- Sinkronisasi status payment via:
  - webhook Midtrans
  - polling Midtrans
  - notifikasi MQTT
- Dispense produk vending dan hadiah game.
- Audit log untuk:
  - request aplikasi
  - login/logout
  - create/update/delete model penting
  - event payment
  - event dispense
  - event permainan
- Dashboard histori transaksi, game history, dan audit log.

## Arsitektur Singkat

1. User checkout di web.
2. Laravel membuat transaksi `pending` dan request QRIS ke Midtrans.
3. Status payment diupdate dari webhook, polling, atau pesan MQTT.
4. Jika Midtrans mengembalikan `capture/settlement`, status internal menjadi `paid`.
5. Laravel finalize transaksi:
   - update `sales_lines`
   - kurangi stok
   - update `dispense_status`
6. Laravel mengirim request ke service hardware `/dispense` per item.
7. Semua aktivitas penting dicatat ke `audit_logs`.

## Requirement

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL/SQLite
- Python 3.10+
- Python package:
  - `flask`
  - `pyserial`
  - `waitress`

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

## Environment Penting

```env
APP_URL=http://localhost:8000

MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_SANITIZE=true
MIDTRANS_3DS=true
MIDTRANS_NOTIFICATION_URL=

MQTT_HOST=127.0.0.1
MQTT_PORT=1883
MQTT_CLIENT_ID=vending-machine-app
MQTT_AUTH_USERNAME=
MQTT_AUTH_PASSWORD=
MQTT_CLEAN_SESSION=true
MQTT_ENABLE_LOGGING=true
MQTT_KEEP_ALIVE_INTERVAL=10
MQTT_PAYMENT_TOPIC=midtrans/payment

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

### Endpoint Notifikasi Midtrans

- `POST /api/webhooks/midtrans`
- kompatibel lama: `POST /api/transaction/notify`

### Mapping Status Midtrans ke Status Internal

- `capture`, `settlement` -> `paid`
- `pending` -> `pending`
- `expire` -> `expired`
- `cancel`, `deny` -> `failed`

Catatan:

- Signature Midtrans diverifikasi untuk webhook HTTP.
- Status yang sudah `paid` tidak diturunkan lagi oleh notifikasi terlambat.
- Event request charge, status check, cancel, expire, webhook, dan update status ikut tercatat di audit log.

## Mode Update Status Pembayaran

### Opsi A: Webhook publik

Set Notification URL Midtrans ke:

`POST https://your-domain.com/api/webhooks/midtrans`

### Opsi B: Polling Midtrans

Jalankan command manual:

```bash
php artisan transactions:sync-pending --limit=50
```

Untuk mode berkelanjutan:

```bash
php artisan schedule:work
```

Scheduler default:

- `transactions:sync-pending --limit=50` tiap 10 detik.

### Opsi C: Notifikasi MQTT

Jika status payment juga dipublikasikan ke topic MQTT, jalankan subscriber:

```bash
php artisan mqtt:subscribe
```

Opsional override:

```bash
php artisan mqtt:subscribe --topic=midtrans/payment --qos=0
```

Catatan:

- Subscriber MQTT akan memproses notifikasi payment ke alur transaksi yang sama dengan webhook.
- Polling tetap bisa dipakai sebagai fallback jika webhook atau MQTT terlambat.
- Payload MQTT minimal perlu memuat `order_id` dan `transaction_status`.
- Field seperti `transaction_id`, `gross_amount`, dan `payment_type` akan dipakai jika tersedia.

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

Contoh jalankan service Python dengan Waitress:

```bash
pip install flask pyserial waitress
waitress-serve --host=127.0.0.1 --port=9000 app:app
```

Catatan:

- Gunakan `127.0.0.1` agar hanya bisa diakses dari mesin lokal.
- Hindari `flask run` atau mode debug untuk operasional harian.
- Saat ini sistem tidak mengandalkan ACK detail dari motor controller untuk menentukan sukses/gagal per item.
- Event request/sukses/gagal dispense ikut tercatat di audit log.

Konfigurasi serial di `app.py`:

- `SERIAL_PORT` (contoh `COM5`)
- `BAUDRATE`
- `MOTOR_SPIN_SECONDS`

## Audit Log

Sistem menyimpan histori aktivitas ke tabel `audit_logs`.

Yang tercatat antara lain:

- request web dan API
- login/logout
- perubahan model inti
- event payment Midtrans
- event dispense
- aktivitas permainan

Dashboard:

- menu `Audit Log`
- route dashboard: `/dashboard/audit-logs`

Sebelum memakai fitur ini, pastikan migration sudah dijalankan:

```bash
php artisan migrate
```

## Operasional yang Direkomendasikan

Untuk lingkungan operasional, jalankan minimal:

- web Laravel
- frontend build/dev server
- service hardware Python
- worker MQTT jika memakai notifikasi topic
- scheduler Laravel jika polling fallback tetap diaktifkan

Contoh:

```bash
php artisan serve
npm run dev
php artisan mqtt:subscribe
php artisan schedule:work
python app.py
```

## Deployment Operasional

Untuk deployment harian, jangan mengandalkan terminal manual. Jalankan proses penting sebagai service/daemon terpisah.

Proses yang sebaiknya dijaga tetap hidup:

- web app Laravel
- worker MQTT: `php artisan mqtt:subscribe`
- scheduler Laravel: `php artisan schedule:work`
- service hardware Python dispenser

### Opsi Windows: NSSM

Jika server menggunakan Windows, `nssm` cocok untuk membungkus command menjadi service.

Contoh service Laravel MQTT worker:

```bash
nssm install VendingMqttWorker
```

Isi parameter:

- `Application`: path ke `php.exe`
- `Startup directory`: root project Laravel
- `Arguments`: `artisan mqtt:subscribe`

Contoh service Laravel scheduler:

```bash
nssm install VendingScheduler
```

Isi parameter:

- `Application`: path ke `php.exe`
- `Startup directory`: root project Laravel
- `Arguments`: `artisan schedule:work`

Contoh service Python dispenser:

```bash
nssm install VendingDispenser
```

Isi parameter:

- `Application`: path ke `python.exe` atau `waitress-serve.exe`
- `Startup directory`: root project
- `Arguments`:
  - jika pakai Python langsung: `app.py`
  - jika pakai Waitress: `--host=127.0.0.1 --port=9000 app:app`

Setelah dibuat:

```bash
nssm start VendingMqttWorker
nssm start VendingScheduler
nssm start VendingDispenser
```

### Opsi Linux: Supervisor

Jika server menggunakan Linux, gunakan `supervisor`.

Contoh konfigurasi `mqtt:subscribe`:

```ini
[program:vending-mqtt]
command=/usr/bin/php /var/www/vending-machine/artisan mqtt:subscribe
directory=/var/www/vending-machine
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/www/vending-machine/storage/logs/mqtt-worker.log
stderr_logfile=/var/www/vending-machine/storage/logs/mqtt-worker-error.log
```

Contoh konfigurasi `schedule:work`:

```ini
[program:vending-scheduler]
command=/usr/bin/php /var/www/vending-machine/artisan schedule:work
directory=/var/www/vending-machine
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/www/vending-machine/storage/logs/scheduler.log
stderr_logfile=/var/www/vending-machine/storage/logs/scheduler-error.log
```

Contoh konfigurasi service dispenser:

```ini
[program:vending-dispenser]
command=/usr/local/bin/waitress-serve --host=127.0.0.1 --port=9000 app:app
directory=/var/www/vending-machine
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/www/vending-machine/storage/logs/dispenser.log
stderr_logfile=/var/www/vending-machine/storage/logs/dispenser-error.log
```

Setelah file config dibuat:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start vending-mqtt
sudo supervisorctl start vending-scheduler
sudo supervisorctl start vending-dispenser
```

### Checklist Production

Sebelum dianggap siap operasional, pastikan:

- `php artisan migrate` sudah dijalankan
- `.env` production sudah benar
- `APP_DEBUG=false`
- key Midtrans sesuai environment
- topic MQTT dan credential benar
- `VENDING_DISPENSE_URL` mengarah ke service lokal yang aktif
- folder `storage/` dan `bootstrap/cache/` writable
- audit log sudah masuk saat login, payment, dan dispense

### Verifikasi Setelah Deploy

Jalankan pengecekan berikut:

```bash
php artisan about
php artisan route:list
php artisan mqtt:subscribe --topic=midtrans/payment
php artisan transactions:sync-pending --limit=10
```

Lalu cek:

- transaksi baru bisa membuat QRIS
- status payment berubah dari webhook/MQTT/polling
- dispense request masuk ke service Python
- data muncul di dashboard `Audit Log`

## Alur UX Landing Page

- Landing page tetap bisa melakukan polling status payment.
- Begitu transaksi `paid`, UI menampilkan sukses dan stok produk ter-update.
- Pada game berhadiah produk, dispense reward juga diproses dari backend.

## Troubleshooting Cepat

- Payment lama berubah status:
  - pastikan webhook aktif, atau worker MQTT aktif, atau scheduler polling berjalan.
- Webhook Midtrans invalid:
  - cek `MIDTRANS_SERVER_KEY` sesuai mode sandbox/production.
- MQTT tidak menerima pesan:
  - cek `MQTT_HOST`, `MQTT_PORT`, credential, dan `MQTT_PAYMENT_TOPIC`.
  - jalankan `php artisan mqtt:subscribe` dan lihat log terminal.
- Dispense tidak terpanggil:
  - pastikan service Flask hidup di `127.0.0.1:9000`.
  - cek `VENDING_DISPENSE_URL` di `.env`.
- Hardware tidak gerak:
  - cek `SERIAL_PORT`, baudrate, kabel, dan izin akses serial.
- Audit log kosong:
  - pastikan migration `audit_logs` sudah dijalankan.

## Catatan

- Webhook, MQTT, dan polling bisa aktif bersamaan. Sistem menahan penurunan status yang tidak valid, jadi notifikasi ganda masih aman secara dasar.
- Jika broker MQTT tidak sepenuhnya trusted, sebaiknya tambahkan validasi payload di level publisher/subscriber.
