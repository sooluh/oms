# Stage 1: Composer Builder
FROM dunglas/frankenphp:1.11.1-php8.4-alpine AS composer-builder

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs

COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY routes ./routes
COPY public ./public
COPY resources ./resources
COPY artisan ./
COPY Caddyfile ./

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev --no-scripts

RUN find /app/vendor -type d \( \
    -name 'tests' -o -name 'test' -o -name 'Tests' \
    -o -name 'docs' -o -name 'doc' -o -name 'examples' \
    \) -exec rm -rf {} + 2>/dev/null || true \
    && find /app/vendor -type f \( \
    -name '*.md' -o -name 'phpunit.xml*' \
    -o -name 'composer.json' -o -name 'composer.lock' \
    -o -name '.gitignore' -o -name '.gitattributes' \
    \) -delete 2>/dev/null || true

# Stage 2: Node Builder
FROM oven/bun:1.3.14-alpine AS node-builder

WORKDIR /app

COPY package.json bun.lock ./

RUN bun install --frozen-lockfile

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
COPY --from=composer-builder /app/vendor ./vendor

RUN bun run build

# Stage 3: Final Image
FROM dunglas/frankenphp:1.11.1-php8.4-alpine

WORKDIR /app

RUN apk add --no-cache \
    icu-libs \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    imagemagick \
    imagemagick-libs \
    gmp \
    tzdata \
    && install-php-extensions \
    bcmath \
    curl \
    dom \
    exif \
    gd \
    gmp \
    imagick \
    intl \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    pgsql \
    redis \
    zip \
    && rm -rf /tmp/* /var/cache/apk/*

COPY --from=composer-builder /app .
COPY --from=node-builder /app/public/build ./public/build
COPY Caddyfile /etc/caddy/Caddyfile
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/entrypoint-app.sh /usr/local/bin/docker-entrypoint.sh

RUN rm -rf \
    /app/storage/framework/views/* \
    /app/storage/framework/cache/data/* \
    /app/storage/logs/*.log \
    /app/bootstrap/cache/* \
    && mkdir -p \
    /app/storage/framework/{sessions,views,cache/data} \
    /app/storage/logs \
    /app/bootstrap/cache \
    /app/public/uploads \
    && mkdir -p /config/caddy /data/caddy \
    && chown -R www-data:www-data /app /config /data \
    && chmod -R 775 /app/storage /app/bootstrap/cache \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD wget -qO- http://localhost:80/up || exit 1

USER www-data

ENV APP_ENV=production
ENV PHP_OPCACHE_ENABLE=1

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
