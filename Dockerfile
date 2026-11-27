# ---------------------------------------------------------------
# ToursTravel Kenya — Dockerfile for Render (single container)
# Render runs ONE container that must listen on $PORT (default 10000).
# This image runs PHP-FPM + Nginx together in that container.
# ---------------------------------------------------------------

# ---- Stage 1: Build frontend assets (Laravel Mix) ----
FROM node:18-alpine AS frontend
WORKDIR /var/www/html

# Install Node dependencies
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

# Copy only what Mix needs to build
COPY webpack.mix.js ./
COPY resources/js ./resources/js
COPY resources/sass ./resources/sass

# Build production assets
RUN npm run production

# ---- Stage 2: PHP runtime + Nginx (Render web service) ----
FROM php:8.2-fpm-alpine

# System packages + PHP extensions required by Laravel
RUN apk add --no-cache \
    nginx \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    gettext \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# PHP dependencies — copy composer files first for better layer caching.
# Note: composer.lock is gitignored in this repo, so install resolves from composer.json.
COPY composer.json ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Application code
COPY . .

# Production frontend assets built in the previous stage
COPY --from=frontend /var/www/html/public/css ./public/css
COPY --from=frontend /var/www/html/public/js ./public/js
COPY --from=frontend /var/www/html/mix-manifest.json ./mix-manifest.json

# Render-specific configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Ensure writable storage/cache directories
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Render provides $PORT; the app must listen on it
EXPOSE 10000

# Entrypoint: migrations, config cache, bind Nginx to $PORT, run services
CMD ["start.sh"]
