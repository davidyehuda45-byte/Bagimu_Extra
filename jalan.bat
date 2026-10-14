@echo off
REM ============================================================
REM  JALAN.CE - Cara termudah menjalankan Bagimu Extra
REM  Tidak butuh Apache/MySQL Laragon. Cukup PHP saja.
REM  Akses: http://localhost:8000
REM ============================================================
cd /d "%~dp0"

where php >nul 2>nul
if errorlevel 1 (
    echo [GAGAL] PHP tidak ditemukan di PATH.
    echo Buka Laragon ^> Menu ^> Terminal, lalu jalankan jalan.bat dari sana.
    pause
    exit /b 1
)

echo [1/4] Cek ekstensi PHP yang dibutuhkan...
php -r "foreach (['zip','fileinfo','mbstring','pdo_sqlite','openssl'] as $e) { echo ' - '.$e.': '.(extension_loaded($e)?'OK':'MATI').PHP_EOL; }"

echo [2/4] Bersihkan cache lama...
php artisan config:clear >nul 2>nul
php artisan view:clear >nul 2>nul

echo [3/4] Siapkan database SQLite...
if not exist "database\database.sqlite" (
    type nul > "database\database.sqlite"
    echo   - file database.sqlite dibuat
)
php artisan migrate --force

echo [4/4] Menjalankan server...
echo.
echo   Buka browser: http://localhost:8000
echo   Tekan Ctrl+C untuk berhenti.
echo.
php artisan serve --host=127.0.0.1 --port=8000
