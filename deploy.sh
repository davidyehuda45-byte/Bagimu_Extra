#!/usr/bin/env bash
# Deploy/update Bagimu Extra di VPS (Ubuntu + PHP 8.2+).
# Cara pakai:  sudo -u www-user ./deploy.sh
# Berhenti saat ada error.
set -euo pipefail
cd "$(dirname "$0")"

echo "[1/7] Mode perawatan..."
php artisan down --render="errors.maintenance" --with-secret="$(php artisan tinker --execute="echo md5(config('app.key'));" 2>/dev/null | tr -d '[:space:]')" || php artisan down || true

echo "[2/7] Ambil kode terbaru..."
git pull --ff-only

echo "[3/7] Dependensi PHP (tanpa dev)..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "[4/7] Migrasi database..."
php artisan migrate --force

echo "[5/7] Aset frontend..."
if [ -f package.json ]; then
  npm ci --no-audit --no-fund
  npm run build
fi
php artisan storage:link || true

echo "[6/7] Optimasi cache production..."
php artisan optimize

echo "[7/7] Verifikasi..."
php artisan deploy:check
php artisan up

echo "SELESAI. Cek https://$(php artisan tinker --execute="echo parse_url(config('app.url'), PHP_URL_HOST);" 2>/dev/null)/up"
