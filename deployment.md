# SetupNesia — Panduan Deployment Lengkap

> Panduan ini mencakup deployment ke **Shared Hosting (cPanel)** dan **VPS Ubuntu 22.04 LTS** menggunakan Nginx + PHP-FPM.

---

## Daftar Isi

- [Persiapan Umum](#persiapan-umum)
- [Option A: Shared Hosting (cPanel)](#option-a-shared-hosting-cpanel)
- [Option B: VPS Ubuntu 22.04 LTS (Nginx)](#option-b-vps-ubuntu-2204-lts-nginx)
- [Konfigurasi Midtrans Production](#konfigurasi-midtrans-production)
- [Queue Worker & Scheduler](#queue-worker--scheduler)
- [Backup Strategy](#backup-strategy)
- [Troubleshooting](#troubleshooting)

---

## Persiapan Umum

### 1. Build Production Assets Secara Lokal

Sebelum upload, build asset frontend terlebih dahulu:

```bash
npm run build
```

Ini akan menghasilkan folder `public/build/` yang berisi file CSS/JS yang sudah diminifikasi.

### 2. File yang TIDAK perlu diupload

Tambahkan ke `.gitignore` (sudah ada) dan **jangan upload**:
- `node_modules/`
- `.env` (buat fresh di server)
- `storage/logs/*.log`
- `.phpunit.result.cache`

### 3. Persiapan Database

Export database lokal jika sudah ada data yang diperlukan:
```bash
php artisan db:seed --class=AdminSeeder  # hanya seeder penting
```

---

## Option A: Shared Hosting (cPanel)

### Prasyarat Shared Hosting
- PHP 8.3+ dengan ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`
- MySQL 8.0+
- Composer tersedia via SSH (atau bisa upload vendor/)
- Akses SSH (sangat direkomendasikan)

---

### Langkah A1: Upload File

**Cara 1: Via Git (Direkomendasikan)**
```bash
# Di server via SSH
cd ~/
git clone https://github.com/YOUR_USERNAME/SetupNesia.git setupnesia_temp
```

**Cara 2: Via File Manager cPanel**
1. Compress project (kecuali `node_modules/` dan `vendor/`) menjadi `.zip`
2. Upload ke direktori `home` di cPanel File Manager
3. Extract di server

---

### Langkah A2: Konfigurasi Document Root

Masalah utama shared hosting: Laravel memerlukan `public/` sebagai document root, tapi hosting biasanya menggunakan `public_html/`.

**Solusi A — Pindahkan konten `public/` ke `public_html/`:**

1. Upload seluruh project ke folder di luar `public_html/`, misalnya `/home/USERNAME/setupnesia/`

2. Pindahkan isi folder `public/` ke `public_html/`:
```bash
# Via SSH
cp -r ~/setupnesia/public/. ~/public_html/
```

3. Edit file `~/public_html/index.php`, ubah path:
```php
<?php

use Illuminate\Http\Request;

// Ubah path ini:
define('LARAVEL_START', microtime(true));

// Path ke autoload Laravel (sesuaikan!)
require __DIR__.'/../setupnesia/vendor/autoload.php';

// Path ke bootstrap aplikasi
$app = require_once __DIR__.'/../setupnesia/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
```

4. Edit `~/public_html/.htaccess` — pastikan sudah ada:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Solusi B — Symlink `public_html` ke `public/` (jika server mendukung):**
```bash
# Via SSH
rm -rf ~/public_html
ln -s ~/setupnesia/public ~/public_html
```

---

### Langkah A3: Install Composer Dependencies

```bash
# Via SSH di folder project
cd ~/setupnesia
composer install --optimize-autoloader --no-dev
```

Jika Composer tidak tersedia di server:
1. Install dependencies di lokal dengan `composer install --no-dev`
2. Upload folder `vendor/` ke server

---

### Langkah A4: Buat Database MySQL

1. Buka **cPanel > MySQL Databases**
2. Buat database baru: `USERNAME_setupnesia`
3. Buat user database dan assign ke database
4. Catat: hostname, database name, username, password

---

### Langkah A5: Konfigurasi .env

```bash
# Via SSH
cd ~/setupnesia
cp .env.example .env
php artisan key:generate
nano .env
```

Isi `.env` production:
```env
APP_NAME=SetupNesia
APP_ENV=production
APP_KEY=      # sudah ter-generate otomatis
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=USERNAME_setupnesia
DB_USERNAME=USERNAME_dbuser
DB_PASSWORD=STRONG_PASSWORD_HERE

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=YOUR_EMAIL_PASSWORD
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="SetupNesia"

# Midtrans PRODUCTION
MIDTRANS_SERVER_KEY=Mid-server-XXXXXXXXXXXXXXXXX
MIDTRANS_CLIENT_KEY=Mid-client-XXXXXXXXXXXXXXXXX
MIDTRANS_IS_PRODUCTION=true
```

---

### Langkah A6: Jalankan Migrasi & Seeder

```bash
cd ~/setupnesia
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

---

### Langkah A7: Optimasi Laravel

```bash
cd ~/setupnesia
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

### Langkah A8: Set Permission File

```bash
cd ~/setupnesia
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 644 .env
```

---

### Langkah A9: SSL Certificate

Aktifkan SSL di cPanel:
1. **cPanel > SSL/TLS > Let's Encrypt SSL** (gratis)
2. Atau gunakan **AutoSSL** yang tersedia di cPanel

Setelah SSL aktif, pastikan redirect HTTP ke HTTPS di `.htaccess`:
```apache
# Tambahkan di atas RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

### Langkah A10: Queue via Cron Job (Shared Hosting)

Shared hosting tidak support daemon process. Gunakan Cron Job:

1. **cPanel > Cron Jobs**
2. Tambahkan cron job setiap menit:
```
* * * * * /usr/local/bin/php /home/USERNAME/setupnesia/artisan queue:work --max-jobs=10 --stop-when-empty >> /dev/null 2>&1
```

3. Tambahkan juga Laravel Scheduler:
```
* * * * * /usr/local/bin/php /home/USERNAME/setupnesia/artisan schedule:run >> /dev/null 2>&1
```

---

---

## Option B: VPS Ubuntu 22.04 LTS (Nginx)

### Prasyarat VPS
- Ubuntu 22.04 LTS
- RAM minimal 1GB (2GB direkomendasikan)
- Akses root/sudo
- Domain yang sudah diarahkan ke IP VPS

---

### Langkah B1: Update Sistem & Install Dependensi

```bash
# Login ke VPS sebagai root
ssh root@YOUR_VPS_IP

# Update sistem
apt update && apt upgrade -y

# Install dependensi dasar
apt install -y curl wget git unzip zip ufw fail2ban
```

---

### Langkah B2: Install PHP 8.3

```bash
# Tambah repository Ondrej PHP
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP 8.3 dan ekstensi yang diperlukan
apt install -y \
    php8.3-fpm \
    php8.3-cli \
    php8.3-mysql \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-zip \
    php8.3-bcmath \
    php8.3-tokenizer \
    php8.3-fileinfo \
    php8.3-openssl \
    php8.3-ctype \
    php8.3-json \
    php8.3-intl \
    php8.3-gd

# Verifikasi
php8.3 --version
```

---

### Langkah B3: Install MySQL 8.0

```bash
# Install MySQL
apt install -y mysql-server

# Amankan instalasi MySQL
mysql_secure_installation
# Ikuti panduan: set root password, remove anonymous users, dll.

# Login ke MySQL
mysql -u root -p

# Buat database dan user
CREATE DATABASE setupnesia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'setupnesia_user'@'localhost' IDENTIFIED BY 'GANTI_DENGAN_PASSWORD_KUAT';
GRANT ALL PRIVILEGES ON setupnesia.* TO 'setupnesia_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

### Langkah B4: Install Nginx

```bash
apt install -y nginx

# Aktifkan dan start Nginx
systemctl enable nginx
systemctl start nginx

# Cek status
systemctl status nginx
```

---

### Langkah B5: Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
composer --version
```

---

### Langkah B6: Install Node.js & NPM

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
node --version
npm --version
```

---

### Langkah B7: Buat User Aplikasi

```bash
# Buat user khusus untuk aplikasi (jangan pakai root)
useradd -m -s /bin/bash setupnesia
usermod -aG www-data setupnesia
```

---

### Langkah B8: Clone Repository

```bash
# Pindah ke direktori web
mkdir -p /var/www/setupnesia
chown setupnesia:www-data /var/www/setupnesia

# Clone project
su - setupnesia
git clone https://github.com/YOUR_USERNAME/SetupNesia.git /var/www/setupnesia
cd /var/www/setupnesia
```

---

### Langkah B9: Install Dependencies

```bash
cd /var/www/setupnesia

# Install PHP dependencies (production mode)
composer install --optimize-autoloader --no-dev

# Install & build Node.js assets
npm ci
npm run build

# Kembali ke root
exit
```

---

### Langkah B10: Konfigurasi .env Production

```bash
cd /var/www/setupnesia

# Copy .env.example
sudo -u setupnesia cp .env.example .env

# Generate app key
sudo -u setupnesia php artisan key:generate

# Edit .env
nano .env
```

Isi file `.env`:
```env
APP_NAME=SetupNesia
APP_ENV=production
APP_KEY=base64:GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=setupnesia
DB_USERNAME=setupnesia_user
DB_PASSWORD=GANTI_DENGAN_PASSWORD_KUAT

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=YOUR_EMAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="SetupNesia"

# Midtrans PRODUCTION
MIDTRANS_SERVER_KEY=Mid-server-XXXXXXXXXXXXXXXXX
MIDTRANS_CLIENT_KEY=Mid-client-XXXXXXXXXXXXXXXXX
MIDTRANS_IS_PRODUCTION=true
```

---

### Langkah B11: Migrasi Database & Seeder

```bash
cd /var/www/setupnesia

sudo -u setupnesia php artisan migrate --force
sudo -u setupnesia php artisan db:seed --force
sudo -u setupnesia php artisan storage:link
```

---

### Langkah B12: Set Permission

```bash
cd /var/www/setupnesia

# Owner
chown -R setupnesia:www-data /var/www/setupnesia

# Permission direktori
find /var/www/setupnesia -type f -exec chmod 644 {} \;
find /var/www/setupnesia -type d -exec chmod 755 {} \;

# Storage dan cache harus writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

### Langkah B13: Konfigurasi Nginx

```bash
nano /etc/nginx/sites-available/setupnesia
```

Isi konfigurasi Nginx:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect HTTP ke HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    root /var/www/setupnesia/public;
    index index.php index.html;

    # SSL (akan dikonfigurasi oleh Certbot)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript
               application/x-javascript application/xml application/json;

    # Max Upload Size
    client_max_body_size 50M;

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Block access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ ^/(\.env|\.git|composer\.(json|lock)|package\.json) {
        deny all;
        return 404;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg|webp)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Logs
    error_log /var/log/nginx/setupnesia_error.log;
    access_log /var/log/nginx/setupnesia_access.log;
}
```

Aktifkan konfigurasi:
```bash
ln -s /etc/nginx/sites-available/setupnesia /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

---

### Langkah B14: Install SSL dengan Let's Encrypt (Certbot)

```bash
# Install Certbot
apt install -y certbot python3-certbot-nginx

# Generate SSL Certificate
certbot --nginx -d yourdomain.com -d www.yourdomain.com \
  --email admin@yourdomain.com \
  --agree-tos \
  --no-eff-email

# Verifikasi auto-renewal
certbot renew --dry-run

# Certbot otomatis menambahkan cron untuk renewal
systemctl status certbot.timer
```

---

### Langkah B15: Optimasi Laravel Production

```bash
cd /var/www/setupnesia

sudo -u setupnesia php artisan config:cache
sudo -u setupnesia php artisan route:cache
sudo -u setupnesia php artisan view:cache
sudo -u setupnesia php artisan event:cache
sudo -u setupnesia php artisan icons:cache 2>/dev/null || true
```

---

### Langkah B16: Setup Supervisor (Queue Worker)

Laravel memerlukan queue worker yang berjalan terus-menerus:

```bash
# Install Supervisor
apt install -y supervisor

# Buat konfigurasi
nano /etc/supervisor/conf.d/setupnesia-worker.conf
```

Isi konfigurasi Supervisor:
```ini
[program:setupnesia-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/setupnesia/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=setupnesia
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/setupnesia/storage/logs/worker.log
stopwaitsecs=3600

[program:setupnesia-scheduler]
process_name=%(program_name)s
command=/bin/bash -c 'while true; do php /var/www/setupnesia/artisan schedule:run --no-interaction >> /var/www/setupnesia/storage/logs/scheduler.log 2>&1; sleep 60; done'
autostart=true
autorestart=true
user=setupnesia
redirect_stderr=true
stdout_logfile=/var/www/setupnesia/storage/logs/scheduler.log
```

Aktifkan Supervisor:
```bash
supervisorctl reread
supervisorctl update
supervisorctl start setupnesia-worker:*
supervisorctl start setupnesia-scheduler
supervisorctl status
```

---

### Langkah B17: Konfigurasi UFW Firewall

```bash
# Aktifkan UFW
ufw default deny incoming
ufw default allow outgoing

# Allow SSH
ufw allow ssh
ufw allow 22/tcp

# Allow HTTP & HTTPS
ufw allow 80/tcp
ufw allow 443/tcp

# Allow MySQL hanya dari localhost (tidak dari luar)
# (default sudah aman karena deny incoming)

# Aktifkan UFW
ufw enable
ufw status verbose
```

---

### Langkah B18: Konfigurasi PHP-FPM

```bash
nano /etc/php/8.3/fpm/pool.d/www.conf
```

Sesuaikan untuk performa:
```ini
; Process Manager
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 3
pm.max_spare_servers = 10
pm.max_requests = 500

; Security
security.limit_extensions = .php
```

```bash
nano /etc/php/8.3/fpm/php.ini
```

Sesuaikan setting:
```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 60
```

Restart PHP-FPM:
```bash
systemctl restart php8.3-fpm
```

---

---

## Konfigurasi Midtrans Production

### 1. Daftarkan Akun Midtrans

1. Kunjungi [dashboard.midtrans.com](https://dashboard.midtrans.com)
2. Daftar/Login dengan akun bisnis
3. Lengkapi verifikasi merchant

### 2. Ambil Production Keys

1. Di dashboard Midtrans: **Settings > Access Keys**
2. Pastikan toggle di kanan atas menunjukkan **"Production"** (bukan Sandbox)
3. Copy **Server Key** dan **Client Key**

### 3. Update .env

```env
MIDTRANS_SERVER_KEY=Mid-server-PRODUCTION_SERVER_KEY
MIDTRANS_CLIENT_KEY=Mid-client-PRODUCTION_CLIENT_KEY
MIDTRANS_IS_PRODUCTION=true
```

### 4. Konfigurasi Webhook di Midtrans Dashboard

1. **Settings > Configuration**
2. Isi **Payment Notification URL**:
   ```
   https://yourdomain.com/payment/callback
   ```
3. Centang **Enable**: `Payment`, `Settlement`, `Deny`
4. Simpan konfigurasi

### 5. Verifikasi Webhook

Test webhook dari Midtrans dashboard atau gunakan:
```bash
# Test endpoint callback (hanya untuk verifikasi status)
curl -X GET https://yourdomain.com/payment/callback
```

---

---

## Queue Worker & Scheduler

### Cek Status Queue Worker

```bash
# VPS
supervisorctl status setupnesia-worker:*

# Cek log
tail -f /var/www/setupnesia/storage/logs/worker.log
```

### Restart Queue Worker (setelah deploy)

Setiap kali deploy code baru, restart queue worker:
```bash
supervisorctl restart setupnesia-worker:*
```

### Artisan Commands Berguna

```bash
# Cek queue yang pending
php artisan queue:monitor

# Clear semua failed jobs
php artisan queue:flush

# Retry failed jobs
php artisan queue:retry all

# Jalankan scheduler manual
php artisan schedule:run
```

---

---

## Backup Strategy

### Setup Backup Otomatis Database (VPS)

```bash
# Buat script backup
nano /usr/local/bin/backup-setupnesia.sh
```

```bash
#!/bin/bash
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/var/backups/setupnesia"
DB_NAME="setupnesia"
DB_USER="setupnesia_user"
DB_PASS="YOUR_DB_PASSWORD"

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$TIMESTAMP.sql.gz

# Backup storage files
tar -czf $BACKUP_DIR/storage_$TIMESTAMP.tar.gz /var/www/setupnesia/storage/app/

# Hapus backup lebih dari 30 hari
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup selesai: $TIMESTAMP"
```

```bash
chmod +x /usr/local/bin/backup-setupnesia.sh

# Tambahkan ke cron (backup setiap hari jam 02:00)
crontab -e
# Tambahkan:
0 2 * * * /usr/local/bin/backup-setupnesia.sh >> /var/log/backup-setupnesia.log 2>&1
```

---

## Proses Deploy Update (CI/CD Manual)

Setiap kali ada update code:

```bash
cd /var/www/setupnesia

# 1. Pull code terbaru
sudo -u setupnesia git pull origin main

# 2. Install/update dependencies
sudo -u setupnesia composer install --optimize-autoloader --no-dev

# 3. Jalankan migrasi baru
sudo -u setupnesia php artisan migrate --force

# 4. Clear dan rebuild cache
sudo -u setupnesia php artisan config:cache
sudo -u setupnesia php artisan route:cache
sudo -u setupnesia php artisan view:cache
sudo -u setupnesia php artisan event:cache

# 5. Restart queue worker
supervisorctl restart setupnesia-worker:*

# 6. Reload Nginx (jika ada perubahan konfigurasi)
nginx -t && systemctl reload nginx
```

Atau gunakan satu script:
```bash
nano /usr/local/bin/deploy-setupnesia.sh
```

```bash
#!/bin/bash
set -e

echo "🚀 Deploying SetupNesia..."

cd /var/www/setupnesia

echo "📥 Pulling latest code..."
sudo -u setupnesia git pull origin main

echo "📦 Installing dependencies..."
sudo -u setupnesia composer install --optimize-autoloader --no-dev

echo "🗄️ Running migrations..."
sudo -u setupnesia php artisan migrate --force

echo "⚡ Caching configuration..."
sudo -u setupnesia php artisan config:cache
sudo -u setupnesia php artisan route:cache
sudo -u setupnesia php artisan view:cache
sudo -u setupnesia php artisan event:cache

echo "🔄 Restarting queue workers..."
supervisorctl restart setupnesia-worker:*

echo "✅ Deployment selesai!"
```

```bash
chmod +x /usr/local/bin/deploy-setupnesia.sh
```

---

## Troubleshooting

### Error: 500 Internal Server Error

```bash
# Cek log Laravel
tail -n 50 /var/www/setupnesia/storage/logs/laravel.log

# Cek log Nginx
tail -n 50 /var/log/nginx/setupnesia_error.log

# Pastikan permission benar
chown -R setupnesia:www-data /var/www/setupnesia/storage
chmod -R 775 /var/www/setupnesia/storage
```

### Error: No Application Encryption Key

```bash
cd /var/www/setupnesia
php artisan key:generate
php artisan config:cache
```

### Error: SQLSTATE Connection Refused

```bash
# Cek MySQL berjalan
systemctl status mysql

# Cek credentials di .env
cat /var/www/setupnesia/.env | grep DB_

# Test koneksi
mysql -u setupnesia_user -p setupnesia
```

### Error: Permission Denied pada Storage

```bash
cd /var/www/setupnesia
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Error: Midtrans Webhook Invalid Signature

Pastikan:
1. `MIDTRANS_SERVER_KEY` di `.env` sesuai dengan yang di dashboard Midtrans
2. URL webhook di Midtrans dashboard sudah benar: `https://yourdomain.com/payment/callback`
3. CSRF exception sudah dikonfigurasi di `bootstrap/app.php`

### Queue Worker Tidak Berjalan

```bash
# Cek status Supervisor
supervisorctl status

# Cek log Supervisor
cat /var/log/supervisor/supervisord.log

# Restart Supervisor
systemctl restart supervisor
supervisorctl reread && supervisorctl update
supervisorctl start setupnesia-worker:*
```

### SSL Certificate Expired

```bash
# Renewal manual
certbot renew

# Cek tanggal expiry
certbot certificates
```

---

## Checklist Final Pre-Launch

Sebelum go-live, pastikan semua checklist terpenuhi di file [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md).

---

*Panduan ini terakhir diperbarui untuk SetupNesia v1.0.0 — Laravel 12, PHP 8.3, MySQL 8.0*
