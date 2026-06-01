<h1 align="center">
  <br>
  <img src="https://img.shields.io/badge/SetupNesia-v1.0.0-7C3AED?style=for-the-badge" alt="SetupNesia">
  <br>
  SetupNesia
  <br>
</h1>

<h4 align="center">
  Platform e-commerce premium untuk keyboard mekanikal, keycaps, deskmat, dan aksesori workspace.
</h4>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL 8">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Midtrans-QRIS-00A0DE?style=flat-square" alt="Midtrans">
  <img src="https://img.shields.io/badge/Tests-49%20Passing-22C55E?style=flat-square" alt="Tests">
</p>

<p align="center">
  <a href="#-fitur-utama">Fitur Utama</a> •
  <a href="#-tech-stack">Tech Stack</a> •
  <a href="#-instalasi-lokal">Instalasi Lokal</a> •
  <a href="#-struktur-proyek">Struktur Proyek</a> •
  <a href="#-deployment">Deployment</a>
</p>

---

## 📦 Fitur Utama

### 🛍️ Storefront (Customer)
- **Landing Page** modern dengan hero section, kategori produk, dan produk unggulan
- **Katalog Produk** dengan filter kategori, pencarian, dan sorting harga
- **Detail Produk** dengan galeri gambar, stok real-time, dan deskripsi lengkap
- **Keranjang Belanja** (Cart) berbasis session dengan update quantity
- **Checkout** dengan form alamat pengiriman dan validasi stok
- **Manajemen Order** — riwayat pesanan, tracking status, dan pembatalan order

### 💳 Pembayaran
- **Midtrans QRIS** terintegrasi via Snap.js popup
- **Webhook Callback** dengan signature verification untuk keamanan
- **Status Payment** otomatis: `pending → paid → failed`
- **Restocking Otomatis** saat order dibatalkan atau gagal

### 🔐 Admin Panel
- **Dashboard** dengan statistik penjualan, order terbaru, dan produk populer
- **Manajemen Produk** — CRUD lengkap dengan upload gambar dan kelola stok
- **Manajemen Kategori** — CRUD kategori produk
- **Manajemen Order** — update status order dan lihat detail
- **Manajemen User** — kelola akun customer
- **Laporan Penjualan** — filter harian/bulanan/tahunan, ekspor CSV, dan cetak PDF

### 🔒 Keamanan & Arsitektur
- **Role-Based Access Control** (RBAC) — `admin` dan `customer`
- **Service Layer Pattern** — business logic terpisah dari controller
- **Form Request Validation** — validasi terpusat dan reusable
- **Policy** — otorisasi berbasis model
- **CSRF Protection** — dengan pengecualian endpoint webhook
- **SEO Optimized** — dynamic meta tags, Open Graph, Twitter Card

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|---|---|
| **Framework** | Laravel 12 (PHP 8.3+) |
| **Auth** | Laravel Breeze |
| **Frontend** | Blade + Tailwind CSS 3 |
| **Database** | MySQL 8.0+ |
| **Payment** | Midtrans QRIS (Snap.js) |
| **Queue** | Database Queue Driver |
| **Cache** | Database Cache Driver |
| **Session** | Database Session Driver |
| **Build Tool** | Vite |
| **Testing** | PHPUnit 12 (49 tests) |

---

## 🚀 Instalasi Lokal

### Prasyarat

Pastikan sudah terinstall:
- **PHP** >= 8.3
- **Composer** >= 2.x
- **Node.js** >= 18.x & **NPM**
- **MySQL** >= 8.0
- **Git**

### Langkah Instalasi

**1. Clone Repository**
```bash
git clone https://github.com/YOUR_USERNAME/SetupNesia.git
cd SetupNesia
```

**2. Install Dependencies PHP**
```bash
composer install
```

**3. Install Dependencies Node.js**
```bash
npm install
```

**4. Konfigurasi Environment**
```bash
cp .env.example .env
php artisan key:generate
```

**5. Konfigurasi Database**

Edit `.env` dan sesuaikan:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=setupnesia
DB_USERNAME=root
DB_PASSWORD=your_password
```

**6. Konfigurasi Midtrans**

Daftar di [dashboard.midtrans.com](https://dashboard.midtrans.com) dan isi:
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-XXXXXXXXXXXXXXXXX
MIDTRANS_CLIENT_KEY=SB-Mid-client-XXXXXXXXXXXXXXXXX
MIDTRANS_IS_PRODUCTION=false
```

**7. Jalankan Migrasi & Seeder**
```bash
php artisan migrate --seed
```

**8. Build Assets**
```bash
npm run build
```

**9. Buat Symlink Storage**
```bash
php artisan storage:link
```

**10. Jalankan Aplikasi**
```bash
# Development mode (semua service sekaligus)
composer run dev

# Atau manual:
php artisan serve
npm run dev
```

Akses aplikasi di: **http://localhost:8000**

### Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| **Admin** | admin@setupnesia.com | password |
| **Customer** | customer@setupnesia.com | password |

---

## 🧪 Testing

Jalankan seluruh test suite:
```bash
php artisan test
```

Atau melalui composer:
```bash
composer test
```

**Output yang diharapkan:**
```
Tests:    49 passed
Duration: ~5 seconds
```

### Daftar Test Suite

| File | Coverage |
|---|---|
| `AuthenticationTest.php` | Login, Register, Logout |
| `AdminProductTest.php` | Admin CRUD Produk |
| `AdminOrderTest.php` | Admin Manajemen Order |
| `CartCheckoutTest.php` | Cart & Checkout Flow |
| `OrderManagementTest.php` | Customer Order & Cancellation |
| `PaymentIntegrationTest.php` | Midtrans Webhook & Signature |
| `ReportManagementTest.php` | Laporan CSV & PDF |
| `StorefrontSeoTest.php` | SEO Meta Tags |

---

## 📁 Struktur Proyek

```
SetupNesia/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin controllers
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── ReportController.php
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── CustomerOrderController.php
│   │   │   ├── PaymentController.php
│   │   │   └── ShopController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/
│   │       ├── Admin/          # Admin Form Requests
│   │       └── CheckoutRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   └── Payment.php
│   ├── Policies/
│   │   └── OrderPolicy.php
│   └── Services/
│       ├── CartService.php
│       ├── CheckoutService.php
│       ├── DashboardStatisticsService.php
│       ├── MidtransService.php
│       ├── OrderService.php
│       └── ReportService.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   └── views/
│       ├── admin/              # Admin panel views
│       ├── cart/
│       ├── checkout/
│       ├── layouts/
│       ├── orders/
│       └── shop/
├── routes/
│   └── web.php
└── tests/
    └── Feature/
        ├── Admin/
        └── Shop/
```

---

## 🌐 Deployment

Lihat panduan lengkap deployment di **[deployment.md](deployment.md)**.

Tersedia panduan untuk:
- ✅ **Shared Hosting** (cPanel/Plesk + Apache)
- ✅ **VPS Ubuntu** (Nginx + PHP-FPM + Supervisor + SSL)

---

## 📋 Daftar Kategori Produk

| Kategori | Deskripsi |
|---|---|
| 🎹 Mechanical Keyboard | Keyboard mekanikal berbagai switch & layout |
| 🔡 Keycaps | Set keycaps custom & artisan |
| 🖱️ Mouse | Gaming & productivity mouse |
| 🗒️ Deskmat | Mousepad & deskmat premium |
| 🖥️ Monitor Stand | Stand monitor ergonomis |
| 🔌 Cable Management | Solusi manajemen kabel |
| ⌨️ Workspace Accessories | Aksesori workspace lainnya |

---

## 👨‍💻 Developer

Dibangun sebagai proyek skripsi menggunakan:
- **Laravel 12** — Backend framework
- **Breeze** — Authentication scaffolding
- **Tailwind CSS** — Utility-first CSS (Dark Modern Theme)
- **Midtrans** — Payment gateway Indonesia

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademis (Skripsi/Tugas Akhir).

---

<p align="center">
  Dibuat dengan ❤️ menggunakan Laravel 12 & Tailwind CSS
</p>
