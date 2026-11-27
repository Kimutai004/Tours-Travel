#!/bin/sh
# ---------------------------------------------------------------
# ToursTravel Kenya — Render entrypoint
# 1. Runs database migrations
# 2. Caches config / routes / views for production performance
# 3. Binds Nginx to Render's $PORT (default 10000)
# 4. Starts PHP-FPM + Nginx in this single container
# ---------------------------------------------------------------
set -e

echo ">>> Running database migrations..."
php artisan migrate --force

echo ">>> Caching Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure the storage symlink exists (public/storage -> ../storage/app/public)
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# Bind Nginx to Render's $PORT
PORT="${PORT:-10000}"
sed -i "s/listen 80;/listen ${PORT};/" /etc/nginx/nginx.conf

echo ">>> Starting PHP-FPM..."
php-fpm -D

echo ">>> Starting Nginx on port ${PORT}..."
exec nginx -g "daemon off;"
