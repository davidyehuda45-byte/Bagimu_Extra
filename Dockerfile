# ==============================================================================
# Multi-stage Dockerfile for Bagimu Extra (Laravel 12)
# ==============================================================================

# ------------------------------------------------------------------------------
# Stage 1: Build PHP dependencies with Composer
# ------------------------------------------------------------------------------
FROM composer:2 AS vendor-builder

WORKDIR /app

# Cache dependencies layer
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

# Copy code required for classmap generation
COPY app/ app/
COPY bootstrap/ bootstrap/
COPY config/ config/
COPY database/ database/
COPY routes/ routes/
COPY artisan ./

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev --ignore-platform-reqs

# ------------------------------------------------------------------------------
# Stage 2: Build frontend assets with Vite & Tailwind CSS
# ------------------------------------------------------------------------------
FROM node:20-alpine AS frontend-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --prefer-offline

# Copy source assets and configuration
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources/ resources/

# Copy Laravel vendor pagination views for Tailwind compilation
COPY --from=vendor-builder /app/vendor/laravel/framework/src/Illuminate/Pagination /app/vendor/laravel/framework/src/Illuminate/Pagination

# Build assets (produces public/build)
RUN npm run build

# ------------------------------------------------------------------------------
# Stage 3: Minimal Production Runtime (Alpine + PHP 8.2-FPM + Nginx + Supervisor)
# ------------------------------------------------------------------------------
FROM php:8.2-fpm-alpine AS runner

# Set timezone
ENV TZ=Asia/Jakarta
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Install Nginx, Supervisor, curl, bash, and tzdata
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    bash \
    tzdata

# Install required PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    pdo_mysql \
    pdo_sqlite \
    zip \
    gd \
    bcmath \
    opcache \
    mbstring \
    && rm /usr/local/bin/install-php-extensions

# Copy Composer binary into runner for maintenance/artisan tasks
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Configure Nginx permissions and user
RUN sed -i 's/user nginx;/user www-data;/' /etc/nginx/nginx.conf \
    && chown -R www-data:www-data /var/lib/nginx /var/log/nginx

WORKDIR /var/www/html

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Copy application code
COPY . /var/www/html

# Backup database code (migrations, seeders) so volume mount does not hide new migrations
COPY database/ /var/www/html/database_files/

# Copy production vendors and compiled frontend assets
COPY --from=vendor-builder /app/vendor /var/www/html/vendor
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Clear any host bootstrap cache, discover packages, and set permissions
RUN rm -f /var/www/html/bootstrap/cache/*.php \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

ENV PORT=80
EXPOSE ${PORT}

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
