#!/bin/sh
set -e

echo "[entrypoint] Starting SiWali..."
echo "[entrypoint] APP_ENV=${APP_ENV:-not set}"

mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    bootstrap/cache

echo "[entrypoint] Caching config..."
php artisan config:cache

echo "[entrypoint] Starting FrankenPHP..."
exec frankenphp run --config /etc/caddy/Caddyfile
