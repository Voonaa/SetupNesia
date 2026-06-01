# SetupNesia — Deployment Checklist Pre-Launch

> Checklist ini wajib diselesaikan sebelum aplikasi go-live ke production.
> Centang setiap item setelah diverifikasi.

---

## 🔐 Keamanan

### Environment & Konfigurasi
- [ ] `APP_DEBUG=false` di `.env` production
- [ ] `APP_ENV=production` di `.env` production
- [ ] `APP_KEY` sudah di-generate dan disimpan aman
- [ ] Tidak ada kredensial sensitif di dalam kode/git repository
- [ ] File `.env` tidak ter-expose (cek di browser: `https://domain.com/.env` harus return 404)
- [ ] File `.git` tidak ter-expose (cek: `https://domain.com/.git` harus return 404)

### Database
- [ ] Password database menggunakan password kuat (min 16 karakter, kombinasi huruf besar/kecil/angka/simbol)
- [ ] User database hanya memiliki akses ke database SetupNesia saja
- [ ] MySQL tidak ter-expose ke internet (hanya localhost)
- [ ] Backup database sudah dikonfigurasi dan berjalan

### SSL & HTTPS
- [ ] SSL certificate sudah aktif dan valid
- [ ] HTTP otomatis redirect ke HTTPS
- [ ] HSTS header sudah dikonfigurasi
- [ ] SSL Labs score minimal A (cek di: https://www.ssllabs.com/ssltest/)

### Server
- [ ] Firewall (UFW) aktif — hanya port 22, 80, 443 yang terbuka
- [ ] Fail2Ban aktif untuk proteksi brute-force SSH
- [ ] User root login via SSH dinonaktifkan (gunakan user biasa + sudo)
- [ ] SSH key-based authentication (disable password auth jika memungkinkan)
- [ ] Semua software/package di-update ke versi terbaru

### Aplikasi
- [ ] CSRF protection aktif di semua form
- [ ] Webhook Midtrans menggunakan signature verification
- [ ] Rate limiting dikonfigurasi untuk endpoint login
- [ ] Security headers dikonfigurasi di Nginx

---

## 💳 Payment Gateway (Midtrans)

- [ ] Akun Midtrans sudah terverifikasi (KYC/KYB selesai)
- [ ] Production Server Key & Client Key sudah diisi di `.env`
- [ ] `MIDTRANS_IS_PRODUCTION=true` di `.env`
- [ ] URL Webhook dikonfigurasi di Midtrans Dashboard: `https://domain.com/payment/callback`
- [ ] Test transaksi production berhasil (min. 1 transaksi sukses)
- [ ] Test webhook callback berfungsi (status order berubah setelah pembayaran)
- [ ] Test transaksi gagal/expired (stok dikembalikan)

---

## 🗄️ Database

- [ ] Semua migrasi sudah dijalankan: `php artisan migrate --status`
- [ ] Seeder sudah dijalankan: admin account aktif
- [ ] Data produk sudah diisi (minimal 10 produk dengan gambar)
- [ ] Data kategori sudah diisi
- [ ] Test login admin berhasil
- [ ] Test login customer berhasil

---

## ⚡ Performa

- [ ] `php artisan config:cache` sudah dijalankan
- [ ] `php artisan route:cache` sudah dijalankan
- [ ] `php artisan view:cache` sudah dijalankan
- [ ] `php artisan event:cache` sudah dijalankan
- [ ] OPcache PHP aktif
- [ ] Assets CSS/JS sudah di-build: `npm run build`
- [ ] Gzip/Brotli compression aktif di Nginx
- [ ] Static assets memiliki Cache-Control header
- [ ] PageSpeed Insights score > 80 (mobile & desktop)

---

## 🔄 Queue & Scheduler

- [ ] Queue worker (Supervisor) berjalan: `supervisorctl status`
- [ ] Laravel Scheduler berjalan setiap menit
- [ ] Test kirim email notifikasi berfungsi (jika ada)
- [ ] Failed jobs monitoring dikonfigurasi
- [ ] Log queue worker tidak ada error

---

## 📧 Email

- [ ] Konfigurasi SMTP email di `.env`
- [ ] Test kirim email dari aplikasi berhasil
- [ ] SPF, DKIM, DMARC DNS records sudah dikonfigurasi (hindari masuk spam)
- [ ] `MAIL_FROM_ADDRESS` menggunakan email domain (bukan gmail/yahoo)

---

## 🌐 Domain & DNS

- [ ] Domain sudah diarahkan ke IP server (A record)
- [ ] `www.` subdomain sudah dikonfigurasi
- [ ] DNS propagation selesai (cek di: https://dnschecker.org)
- [ ] `APP_URL` di `.env` sudah diupdate dengan domain production

---

## 📁 File & Storage

- [ ] `php artisan storage:link` sudah dijalankan
- [ ] Direktori `storage/` writable oleh web server
- [ ] Direktori `bootstrap/cache/` writable
- [ ] Upload gambar produk berfungsi
- [ ] Gambar produk tampil di frontend

---

## 🧪 Fungsionalitas

### Storefront
- [ ] Landing page tampil dengan benar
- [ ] Halaman katalog produk tampil dengan benar
- [ ] Detail produk tampil dengan benar + gambar
- [ ] Filter kategori berfungsi
- [ ] Pencarian produk berfungsi
- [ ] Tambah ke cart berfungsi
- [ ] Update quantity di cart berfungsi
- [ ] Hapus item dari cart berfungsi

### Checkout & Payment
- [ ] Flow checkout lengkap berfungsi
- [ ] Validasi form checkout berfungsi
- [ ] QRIS Midtrans Snap popup muncul
- [ ] QR code tampil dan bisa di-scan
- [ ] Setelah bayar, status order berubah ke "paid"
- [ ] Stok produk berkurang setelah order

### Customer
- [ ] Register akun berfungsi
- [ ] Login/logout berfungsi
- [ ] Riwayat order tampil
- [ ] Detail order tampil
- [ ] Batalkan order berfungsi (jika status masih pending)
- [ ] Stok kembali setelah order dibatalkan

### Admin Panel
- [ ] Login admin berfungsi
- [ ] Dashboard statistik tampil
- [ ] Tambah produk baru berfungsi (dengan upload gambar)
- [ ] Edit produk berfungsi
- [ ] Hapus produk berfungsi
- [ ] Tambah/edit kategori berfungsi
- [ ] Update status order berfungsi
- [ ] Kelola user berfungsi
- [ ] Laporan harian/bulanan/tahunan tampil
- [ ] Export CSV berfungsi
- [ ] Cetak PDF berfungsi

---

## 📊 Monitoring & Logging

- [ ] Log aplikasi Laravel berfungsi: `storage/logs/laravel.log`
- [ ] Log Nginx error dipantau
- [ ] Log Supervisor (queue worker) dipantau
- [ ] Konfigurasi log rotation (`logrotate`) untuk mencegah disk penuh
- [ ] Uptime monitoring dikonfigurasi (contoh: UptimeRobot — gratis)
- [ ] Disk space monitoring dikonfigurasi

---

## 📱 Cross-browser & Mobile

- [ ] Tampilan di Chrome (desktop) ✓
- [ ] Tampilan di Firefox (desktop) ✓
- [ ] Tampilan di Safari (desktop) ✓
- [ ] Tampilan di Chrome (mobile/Android) ✓
- [ ] Tampilan di Safari (mobile/iOS) ✓
- [ ] Tampilan di Edge (desktop) ✓

---

## 🔍 SEO

- [ ] Title tag unik di setiap halaman
- [ ] Meta description ada di setiap halaman
- [ ] Open Graph tags ada (untuk share di media sosial)
- [ ] Sitemap.xml tersedia (jika dikonfigurasi)
- [ ] robots.txt ada dan benar
- [ ] Google Search Console sudah terhubung (opsional)

---

## 📋 Dokumentasi

- [ ] README.md sudah diupdate dan akurat
- [ ] deployment.md tersedia untuk referensi
- [ ] Akun admin dan credentials tersimpan aman di password manager
- [ ] Midtrans credentials tersimpan aman

---

## ✅ Final Sign-off

| Item | Status | Verified By | Date |
|------|--------|-------------|------|
| Security Review | ⬜ | | |
| Functional Testing | ⬜ | | |
| Performance Testing | ⬜ | | |
| Payment Testing | ⬜ | | |
| Stakeholder Approval | ⬜ | | |

---

**Status Deployment:**
- [ ] 🟡 In Progress
- [ ] 🟢 Live - Production Ready
- [ ] 🔴 Blocked - Issues Found

---

*SetupNesia v1.0.0 — Deployment Checklist*
