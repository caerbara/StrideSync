#!/bin/bash
# Digital Ocean App Platform deploy hook
# This script runs after deployment

set -e

echo "Running post-deploy tasks..."

# Run database migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link if it doesn't exist
php artisan storage:link || true

echo "Post-deploy tasks completed!"
