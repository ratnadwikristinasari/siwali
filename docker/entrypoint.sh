#!/bin/sh
set -e

if [ "$1" = "php" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
fi

exec "$@"
