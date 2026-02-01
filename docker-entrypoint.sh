#!/bin/bash
set -e

# Set proper permissions for Laravel directories
mkdir -p storage/app/private/livewire-tmp
chown -R www-data:www-data storage bootstrap/cache storage/app/private/livewire-tmp || true
chmod -R 775 storage bootstrap/cache storage/app/private/livewire-tmp || true

# Clear Laravel caches if they exist (helpful for debugging)
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan storage:link || true

# Run database migrations (optional - uncomment if needed)
# php artisan migrate --force || true

# Cache configuration for better performance (only if .env exists)
if [ -f .env ]; then
    # php artisan config:cache || true
    # php artisan route:cache || true
    # php artisan view:cache || true
    echo "Skipping cache for debugging..."
fi

# Execute the command passed to the container
if [ "$1" = 'apache2-foreground' ]; then
    exec apache2-foreground
else
    exec "$@"
fi
