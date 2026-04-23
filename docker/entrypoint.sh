#!/bin/sh

# Exit on error
set -e

# Wait for MySQL to be ready if DB_HOST is set
if [ "$DB_HOST" = "mysql" ]; then
    echo "Waiting for MySQL..."
    while ! nc -z mysql 3306; do
      sleep 1
    done
    echo "MySQL is ready!"
fi

# Run migrations
php artisan migrate --force

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM in the background
php-fpm -D

# Start Nginx in the foreground
echo "Antigravity Engine Started!"
nginx -g "daemon off;"
