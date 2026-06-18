#!/bin/sh
set -e

cd /app

echo "=> Verifying directory structure..."
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/api-docs \
    storage/app/public \
    storage/app/private \
    storage/app/data \
    storage/app/tmp \
    bootstrap/cache \
    public/uploads
rm -f bootstrap/cache/*.php

export HOME=/tmp

echo "=> Caching Laravel configuration..."
php artisan config:cache --no-interaction

echo "=> Running migrations..."
php artisan migrate --force --no-interaction
echo "=> Migrations complete."

echo "=> Running Filament optimizations..."
php artisan filament:optimize --no-interaction || echo "WARN: filament:optimize failed, continuing..."
php artisan filament:cache-components --no-interaction || echo "WARN: filament:cache-components failed, continuing..."

echo "=> Starting: $*"
exec "$@"
