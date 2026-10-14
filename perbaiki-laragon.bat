@echo off
REM ============================================================
REM  PERBAIKI-LARAGON.CE - Perbaikan permanen (cukup 1x jalan)
REM  1. Aktifkan extension=zip di php.ini Laragon (wajib utk .xlsx)
REM  2. Bersihkan cache view/config basi (penyebab error tiap start)
REM  3. Pastikan database + APP_KEY siap
REM  Jalankan, lalu RESTART Laragon (Stop All + Start All).
REM ============================================================
cd /d "%~dp0"
setlocal EnableDelayedExpansion

echo == [1/4] Cari php.ini Laragon...
set "LARAGON_PHP_INI="
for /d %%D in (C:\laragon\bin\php\*) do (
    if exist "%%D\php.ini" set "LARAGON_PHP_INI=%%D\php.ini"
)
if not defined LARAGON_PHP_INI (
    echo   - php.ini Laragon tidak ketemu. Lewati (pastikan Laragon terinstal di C:\laragon^).
) else (
    echo   - ketemu: %LARAGON_PHP_INI%
    findstr /b /c:"extension=zip" "%LARAGON_PHP_INI%" >nul
    if errorlevel 1 (
        echo   - mengaktifkan extension=zip...
        powershell -NoProfile -Command "(Get-Content '%LARAGON_PHP_INI%') -replace '^;extension=zip\s*$','extension=zip' | Set-Content '%LARAGON_PHP_INI%'"
        findstr /b /c:"extension=zip" "%LARAGON_PHP_INI%" >nul
        if errorlevel 1 (
            echo   - GAGAL otomatis. Edit manual: buka Laragon ^> PHP ^> php.ini, hapus titik-koma pada ;extension=zip
        ) else (
            echo   - extension=zip AKTIF.
        )
    ) else (
        echo   - extension=zip sudah aktif. OK.
    )
)

echo == [2/4] Bersihkan cache Laravel...
php artisan optimize:clear

echo == [3/4] Siapkan aplikasi...
if not exist "database\database.sqlite" (
    type nul > "database\database.sqlite"
    echo   - file database.sqlite dibuat
)
php artisan migrate --force
php artisan storage:link >nul 2>nul

echo == [4/4] Verifikasi...
php -r "echo ' - zip (PHP ini): '.(extension_loaded('zip')?'OK':'MATI').PHP_EOL;"
php artisan view:cache >nul 2>nul && echo   - template terkompilasi ulang. OK.

echo.
echo SELESAI. Sekarang di Laragon: klik "Stop All", lalu "Start All".
echo Akses utama : http://bagimuextra.test  (via Apache Laragon)
echo Alternatif  : jalan.bat  (http://localhost:8000, tanpa Apache/MySQL)
echo.
pause
