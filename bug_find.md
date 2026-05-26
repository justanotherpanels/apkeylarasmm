# Bug Findings Tracker

Tanggal audit: 2026-05-22
Project: `/Volumes/Project/Laravel/apkeysmm`

## Cara pakai
- [ ] = belum dikerjakan
- [~] = sedang dikerjakan
- [x] = sudah selesai

## Major

- [x] Race condition saldo saat order (bisa menyebabkan double debit/negative balance pada request paralel).  
  Lokasi:
  - `app/Http/Controllers/Member/SmmController.php`
  - `app/Http/Controllers/ApiFrontend/OrderController.php`
  - `app/Http/Controllers/ApiController.php`
  Catatan perbaikan: sudah dipindah ke `DB::transaction()` + `lockForUpdate()` dan re-check saldo di dalam transaksi.

- [x] SSL verification dimatikan di banyak outgoing API call (`Http::withoutVerifying()`), berisiko MITM jika kebawa non-local.  
  Lokasi utama:
  - `app/Http/Controllers/Member/SmmController.php`
  - `app/Http/Controllers/Member/PaymentController.php`
  - `app/Http/Controllers/ApiController.php`
  - `app/Http/Controllers/ApiFrontend/OrderController.php`
  - `app/Http/Controllers/ApiFrontend/DepositController.php`
  - `app/Http/Controllers/Admin/SmmController.php`
  - `app/Services/WhatsAppBotService.php`
  Catatan perbaikan: sudah diganti ke `Http::external()` (macro global) dengan kontrol env `HTTP_VERIFY_SSL`.

- [x] Node WhatsApp server belum ada auth pada endpoint internal (`/session/start`, `/session/send`, `/session/logout`) + CORS terbuka.  
  Lokasi:
  - `whatsapp-server/index.js`
  Catatan perbaikan: sudah ditambah `X-Server-Token` check, bind host configurable (`WHATSAPP_SERVER_HOST`), dan CORS allowlist env.

- [x] Webhook WhatsApp publik tanpa verifikasi signature/auth (bisa spoof event/message).  
  Lokasi:
  - `routes/web.php`
  - `app/Http/Controllers/Admin/WhatsAppController.php`
  - `app/Http/Controllers/Admin/WhatsAppBotController.php`
  Catatan perbaikan: sudah ditambah validasi header `X-Webhook-Secret` di endpoint webhook Laravel, dan Node mengirim header secret.

- [x] OTP flow belum punya expiry, attempt limit, dan rate limit; rawan brute force & user enumeration.  
  Lokasi:
  - `app/Http/Controllers/AuthController.php`
  - `app/Http/Controllers/ApiFrontend/AuthController.php`
  Catatan perbaikan: sudah ditambah kolom OTP security (`otp_expires_at`, `otp_attempts`, `otp_last_sent_at`), throttle route OTP API/web, cooldown resend, max attempts, dan response forget dibuat generik.

- [x] Open redirect via URL callback dari input client (`redirect()->away($url_return/url_success/url_cancel)`).  
  Lokasi:
  - `app/Http/Controllers/Member/PaymentController.php`
  Catatan perbaikan: sudah ditambah validasi URL (scheme http/https) + mode strict host allowlist via env `PAYMENT_REDIRECT_STRICT_HOSTS` & `PAYMENT_REDIRECT_ALLOWED_HOSTS`.

- [x] Head/Footer script injection (stored XSS surface) karena render raw HTML/JS dari setting.  
  Lokasi:
  - `resources/views/layouts/member/master.blade.php`
  - `resources/views/auth/login.blade.php`
  - `resources/views/auth/forget.blade.php`
  - `resources/views/auth/register.blade.php`
  - `resources/views/welcome.blade.php`
  Catatan perbaikan: sudah ditambah audit log perubahan di SettingsController.

## Minor / Hardening

- [ ] Konfigurasi environment masih mode local/debug aktif.  
  Lokasi:
  - `.env` (`APP_ENV=local`, `APP_DEBUG=true`)
  Catatan: untuk production set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` domain real.

- [ ] Secret/session artifact WhatsApp tersimpan di project (`auth_info/.../creds.json` dkk).  
  Lokasi:
  - `whatsapp-server/auth_info/`
  Catatan: jangan commit/deploy; masukkan ke ignore dan pisahkan storage aman.

- [x] File sample/demo PHP berada di public path (surface tambahan yang tidak perlu).  
  Lokasi contoh:
  - `public/assets/ajax/post.php`
  - `public/themes/ajax/server-processing.php`
  Catatan: sudah dihapus.

- [x] Beberapa aksi state-changing masih lewat `GET` (sinkronisasi status), lebih aman `POST`.  
  Lokasi:
  - route sync order/deposit di `routes/web.php`
  Catatan: sudah dipindahkan ke `POST` dan lindungi CSRF/auth sesuai konteks.

## Dependency Security Audit (2026-05-22)

- [x] Composer advisory ditemukan (5 advisory):
  - `symfony/http-kernel` (CVE-2026-45075)
  - `symfony/mailer` (CVE-2026-45068)
  - `symfony/mime` (CVE-2026-45070, CVE-2026-45067)
  - `symfony/routing` (CVE-2026-45065)
  Catatan: sudah update dependency Laravel/Symfony ke versi patched (composer update).

- [x] NPM audit `whatsapp-server` (production deps): 0 vulnerability saat audit terakhir.

## Progress Log

- 2026-05-22: Audit awal dibuat.
- 2026-05-22: Patch mayor tahap 1 selesai (race condition saldo, SSL verify by env, hardening webhook WA, hardening OTP, update env example).
- 2026-05-26: Patch tahap 2 selesai (fix SSL verify WhatsAppBotService.php, audit log head/footer, route sync ke POST, hapus file sample PHP di public, composer update untuk fix vulnerability Symfony).
