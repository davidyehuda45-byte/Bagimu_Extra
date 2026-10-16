#!/bin/sh
set -e

# Port configuration (default 80)
PORT="${PORT:-80}"
sed -i "s/__PORT__/${PORT}/g" /etc/nginx/http.d/default.conf

# Ensure essential directories exist
mkdir -p \
    /var/www/html/storage/app/public \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    /var/www/html/database

# Check SQLite DB file if sqlite driver is active
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ "$DB_PATH" != ":memory:" ]; then
        mkdir -p "$(dirname "$DB_PATH")"
        if [ ! -f "$DB_PATH" ]; then
            touch "$DB_PATH"
        fi
        chown www-data:www-data "$DB_PATH"
        chmod 664 "$DB_PATH"
    fi
fi

# Clear any stale bootstrap cache and rediscover production packages
rm -f /var/www/html/bootstrap/cache/*.php
php artisan package:discover --ansi 2>/dev/null || true

# Ensure storage symlink exists
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link --force 2>/dev/null || true
fi

# Set proper permissions for storage and bootstrap cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Production cache optimization (only if APP_KEY is set and APP_ENV is production)
if [ "${APP_ENV}" = "production" ] && [ -n "${APP_KEY}" ]; then
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
fi

# Sync fresh migrations and seeders from image into mounted database volume
if [ -d /var/www/html/database_files ]; then
    echo "==> Sinkronisasi file migrasi & seeder dari image ke volume..."
    mkdir -p /var/www/html/database/migrations /var/www/html/database/seeders
    cp -rf /var/www/html/database_files/migrations/* /var/www/html/database/migrations/ 2>/dev/null || true
    cp -rf /var/www/html/database_files/seeders/* /var/www/html/database/seeders/ 2>/dev/null || true
fi

# 1. Migrasi database secara idempotent
if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
    echo "==> [1/3] Menjalankan migrasi database (idempotent)..."
    php artisan migrate --force
fi

# 2. Seeder data awal secara idempotent
if [ "${AUTO_SEED:-true}" = "true" ]; then
    echo "==> [2/3] Menjalankan seeder data awal (idempotent)..."
    ALLOW_PROD_SEED=true php artisan db:seed --force
fi

# 3. Jalankan sistem aplikasi (Supervisord: Nginx + PHP-FPM)
echo "==> [3/3] Menjalankan sistem aplikasi..."
exec "$@"
