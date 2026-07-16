FROM node:22-alpine AS build-js
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --frozen-lockfile

COPY resources/ resources/
COPY vite.config.js ./
COPY public/ public/

RUN npm run build

FROM dunglas/frankenphp:php8.4-alpine AS php-base

RUN apk add --no-cache \
      libpng \
      libzip \
      zlib \
      oniguruma \
      freetype \
      libjpeg-turbo \
      ca-certificates \
      curl \
  && install-php-extensions \
      pdo_mysql \
      gd \
      zip \
      bcmath \
      pcntl \
      redis \
      opcache \
      sockets \
  && rm -rf /var/cache/apk/* /tmp/*

FROM php-base AS builder

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
      --no-dev \
      --no-scripts \
      --no-autoloader \
      --no-interaction \
      --prefer-dist \
  && rm -rf /root/.composer/cache

COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

RUN cp vendor/laravel/octane/src/Commands/stubs/frankenphp-worker.php public/frankenphp-worker.php

RUN php artisan route:cache \
  && php artisan view:cache \
  && php artisan event:cache

RUN \
  find vendor -type d \( \
       -name 'tests' -o -name 'Tests' -o \
       -name 'test'  -o -name 'Test'  -o \
       -name 'spec'  -o -name 'Spec'  -o \
       -name 'benchmarks' -o -name 'examples' \
     \) -exec rm -rf {} + 2>/dev/null || true \
  # Hapus dokumentasi (tidak pernah di-autoload)
  && find vendor -maxdepth 4 \( \
       -name '*.md' -o -name 'CHANGELOG*' -o \
       -name 'CONTRIBUTING*' -o -name 'UPGRADING*' -o \
       -name 'HISTORY*' -o -name 'phpunit.xml*' -o \
       -name '.travis.yml' -o -name '.styleci.yml' \
     \) -delete 2>/dev/null || true

FROM php-base AS final

WORKDIR /app

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php/production.ini "$PHP_INI_DIR/conf.d/99-production.ini"

COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN mkdir -p \
      /data/caddy \
      /config/caddy \
      storage/framework/sessions \
      storage/framework/views \
      storage/framework/cache \
      storage/logs \
      bootstrap/cache \
  && chown -R www-data:www-data \
      /data/caddy \
      /config/caddy \
      storage \
      bootstrap/cache \
  && chmod -R 755 storage bootstrap/cache

COPY --from=builder --chown=www-data:www-data /app /app

COPY --from=build-js --chown=www-data:www-data /app/public/build /app/public/build

RUN rm -f \
      .env.staging \
      .env.production \
      .env.local \
      .env \
      composer.lock \
      package.json \
      package-lock.json \
      vite.config.js \
      .env.example \
      .gitattributes \
      AGENTS.md \
      PRD.md \
  && rm -rf \
      node_modules \
      .git \
      tests \
      docker \
      k8s \
      README.md \
      Dockerfile* \
      .github \
      .editorconfig \
  && rm -f frankenphp

LABEL org.opencontainers.image.source="https://github.com/jtinovation/siwali" \
      org.opencontainers.image.description="SiWali - Sistem Informasi Perwalian" \
      org.opencontainers.image.licenses="MIT"

EXPOSE 8000
EXPOSE 2019

USER www-data

HEALTHCHECK --interval=15s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -sf http://localhost:8000/up || exit 1

ENTRYPOINT ["/entrypoint.sh"]
