#!/bin/sh
set -e

cd /var/www/html

# Cache config, routes, views (now that env vars are available)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start the default entrypoint (S6 supervisor -> nginx + php-fpm)
exec /init
