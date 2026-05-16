#!/bin/sh
set -e

cd /var/www/html

echo "[entrypoint] Apotek Zema — starting..."

php artisan package:discover --ansi 2>/dev/null || true

mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

if [ -n "$DB_HOST" ] && [ "$DB_HOST" != "127.0.0.1" ] && [ "$DB_HOST" != "localhost" ]; then
    echo "[entrypoint] Waiting for database at $DB_HOST:${DB_PORT:-5432}..."
    i=0
    until php artisan db:show >/dev/null 2>&1 || [ $i -ge 30 ]; do
        i=$((i + 1))
        sleep 2
    done
    if php artisan db:show >/dev/null 2>&1; then
        echo "[entrypoint] Database connection OK"
    else
        echo "[entrypoint] WARNING: Database not reachable — app may error on requests"
    fi
fi

if [ ! -L public/storage ]; then
    php artisan storage:link --force 2>/dev/null || true
fi

if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "[entrypoint] Running migrations..."
    php artisan migrate --force --no-interaction
fi

if [ "$APP_ENV" = "production" ] || [ "$RUN_OPTIMIZE" = "true" ]; then
    echo "[entrypoint] Optimizing Laravel..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "[entrypoint] Ready. Starting supervisord..."

exec "$@"
