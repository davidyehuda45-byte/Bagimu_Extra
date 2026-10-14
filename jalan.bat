@echo off
REM ============================================================
REM  JALAN.BAT - Menjalankan Bagimu Extra
REM  Akses: http://localhost:8000
REM ============================================================
cd /d "%~dp0"

where php >nul 2>nul
if errorlevel 1 (
    echo [GAGAL] PHP tidak ditemukan di PATH.
    echo Buka Terminal XAMPP / Laragon, lalu jalankan jalan.bat dari sana.
    pause
    exit /b 1
)

echo [1/4] Cek ekstensi PHP yang dibutuhkan...
php -r "foreach (['zip','fileinfo','mbstring','pdo_mysql','openssl'] as $e) { echo ' - '.$e.': '.(extension_loaded($e)?'OK':'MATI').PHP_EOL; }"

echo [2/4] Bersihkan cache lama...
php artisan config:clear >nul 2>nul
php artisan view:clear >nul 2>nul

echo [3/4] Migrasi database...
php artisan migrate --force

echo [4/4] Menjalankan server...
echo.
echo   Buka browser: http://localhost:8000
echo   Tekan Ctrl+C untuk berhenti.
echo.
php artisan serve --host=127.0.0.1 --port=8000

