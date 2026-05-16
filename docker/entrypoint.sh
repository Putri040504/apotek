#!/bin/sh
set -e

cd /var/www/html

echo "[entrypoint] Apotek Zema — starting..."

# Pastikan folder writable
mkdir -p storage/framework/{sessions,views,cache/data} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

# Tunggu database (opsional, max ~60 detik)
if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ] && [ "$DB_HOST" != "localhost" ]; then
    echo "[entrypoint] Waiting for database at $DB_HOST:${DB_PORT:-3306}..."
    i=0
    until php artisan db:show >/dev/null 2>&1 || [ $i -ge 30 ]; do
        i=$((i + 1))
        sleep 2
    done
fi

# Storage link
if [ ! -L public/storage ]; then
    php artisan storage:link --force 2>/dev/null || true
fi

# Migrasi (aktifkan di Dokploy: RUN_MIGRATIONS=true)
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "[entrypoint] Running migrations..."
    php artisan migrate --force --no-interaction
fi

# Cache config untuk production
if [ "$APP_ENV" = "production" ] || [ "$RUN_OPTIMIZE" = "true" ]; then
    echo "[entrypoint] Optimizing Laravel..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "[entrypoint] Ready. Starting supervisord..."

exec "$@"
