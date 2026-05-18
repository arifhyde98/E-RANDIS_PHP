#!/bin/bash
set -e

# Terapkan kepemilikan folder yang benar untuk user web server
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate key jika belum terbuat
if [ -z "$APP_KEY" ]; then
    echo "Menghasilkan APP_KEY baru..."
    php artisan key:generate --force
fi

# 🌟 PENTING: Jalankan migrasi database paling awal agar semua tabel driver (session, cache, queue) terbuat!
echo "Menjalankan migrasi database..."
php artisan migrate --force || echo "Migrasi gagal (akan dicoba lagi oleh pengguna secara manual jika database belum siap)."

# Jalankan optimasi Laravel (gunakan || true agar jika cache gagal tidak menghentikan boot kontainer)
echo "Membersihkan cache konfigurasi..."
php artisan config:clear || true
php artisan cache:clear || true

# Menjalankan Supervisor
echo "Menjalankan php-fpm dan nginx via Supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

