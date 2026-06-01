#!/bin/bash
set -e

# Set proper permissions for Laravel directories
mkdir -p storage/app/private/livewire-tmp
chown -R www-data:www-data storage bootstrap/cache storage/app/private/livewire-tmp || true
chmod -R 775 storage bootstrap/cache storage/app/private/livewire-tmp || true

# Create storage symlink
php artisan storage:link || true

# Run database migrations automatically
php artisan migrate --force || true

# Enable production caching
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "✅ Laravel startup complete"

# Execute the command passed to the container
exec "$@"
