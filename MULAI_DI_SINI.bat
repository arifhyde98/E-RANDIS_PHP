@echo off
echo ===================================================
echo   E-RANDIS INITIAL SETUP WIZARD
echo ===================================================
echo.

if not exist .env (
    echo [1/4] Menduplikasi file .env...
    copy .env.example .env
) else (
    echo [1/4] File .env sudah ada.
)

echo [2/4] Menginstall dependencies (Composer)...
call composer install

echo [3/4] Generate Application Key...
php artisan key:generate

echo [4/4] Menginstall dependencies Frontend (NPM)...
call npm install

echo [5/4] Membangun Aset (Build CSS/JS)...
call npm run build

echo [6/4] Membuat Link Storage...
php artisan storage:link

echo.
echo ===================================================
echo   SETUP SELESAI!
echo ===================================================
echo.
echo Langkah selanjutnya:
echo 1. Pastikan database sudah dibuat di Laragon/MySQL.
echo 2. Cek file .env dan sesuaikan DB_DATABASE.
echo 3. Jalankan 'php artisan migrate --seed' di terminal.
echo.
pause
