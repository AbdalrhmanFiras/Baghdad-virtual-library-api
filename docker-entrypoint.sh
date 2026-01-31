#!/bin/bash
set -e

# Set proper permissions for Laravel directories
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# Clear Laravel caches if they exist (helpful for debugging)
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run database migrations (optional - uncomment if needed)
# php artisan migrate --force || true

# Cache configuration for better performance (only if .env exists)
if [ -f .env ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Execute the command passed to the container
if [ "$1" = 'apache2-foreground' ]; then
    exec apache2-foreground
else
    exec "$@"
fi
