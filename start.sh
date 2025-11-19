#!/bin/sh

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations
php artisan migrate --force

# Seed database (ignore if already seeded)
php artisan db:seed --force || true

# Create storage link
php artisan storage:link || true

# Start server
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
