#!/bin/sh
set -e

# Run migrations
php artisan migrate --force

# Start the default entrypoint
exec /init
