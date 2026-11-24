# syntax=docker/dockerfile:1

FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
ARG ASSET_URL
ENV ASSET_URL=${ASSET_URL}
RUN npm ci --no-audit --no-fund
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
    --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative \
    && find vendor -type d \( -iname tests -o -iname test -o -iname docs -o -iname examples \) -prune -exec rm -rf {} + \
    && find vendor -type f \( -iname '*.md' -o -iname '*.txt' -o -iname 'LICENSE*' -o -iname '.git*' -o -iname 'phpunit*.xml*' \) -delete

FROM dunglas/frankenphp:1-php8.4-alpine AS runtime

RUN install-php-extensions pdo_mysql redis opcache pcntl gd zip bcmath sockets \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini "$PHP_INI_DIR/conf.d/zz-app.ini"

ARG UID=10001
ENV XDG_CONFIG_HOME=/tmp/caddy-config \
    XDG_DATA_HOME=/tmp/caddy-data \
    APP_ENV=production \
    APP_DEBUG=false \
    OCTANE_SERVER=frankenphp

RUN adduser -D -u ${UID} app \
    && mkdir -p /app /tmp/caddy-config /tmp/caddy-data \
    && chown -R app:app /app /tmp/caddy-config /tmp/caddy-data

WORKDIR /app
COPY --from=vendor --chown=app:app /app /app
COPY --from=assets --chown=app:app /app/public/build /app/public/build

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && cp vendor/laravel/octane/src/Commands/stubs/frankenphp-worker.php public/frankenphp-worker.php \
    && chown -R app:app storage bootstrap/cache public/frankenphp-worker.php

USER app

EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD wget -q -O /dev/null http://127.0.0.1:8080/up || exit 1

COPY --chown=app:app --chmod=755 docker/entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
CMD ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8080", "--admin-port=2019", "--max-requests=500"]
