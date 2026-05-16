#!/bin/sh
set -e

cd /var/www/html

echo "[entrypoint] Apotek Zema — starting..."

php artisan package:discover --ansi 2>/dev/null || true

mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true


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

    if php artisan route:cache; then
        echo "[entrypoint] Route cache OK"
    else
        echo "[entrypoint] WARNING: route:cache failed — app tetap jalan tanpa route cache"
        php artisan route:clear
    fi

    php artisan view:cache
fi

echo "[entrypoint] Starting supervisord (Apache listens on 0.0.0.0:80)..."
echo "[entrypoint] Setelah deploy: curl http://127.0.0.1/up di Terminal Dokploy untuk tes"

exec "$@"
