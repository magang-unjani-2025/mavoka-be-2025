#!/bin/sh

LARAVEL_ROOT="/var/www/html"
WEB_USER="www-data"

# Change group ownership
chgrp -R $WEB_USER $LARAVEL_ROOT

# Allow write to these directories
chmod -R 775 \
    $LARAVEL_ROOT/public \
    $LARAVEL_ROOT/storage \
    $LARAVEL_ROOT/bootstrap/cache

# Read only for the rest of the files and directories
fdfind . $LARAVEL_ROOT \
    --type file \
    --exclude public \
    --exclude storage \
    --exclude bootstrap/cache \
    --exec chmod 644

fdfind . $LARAVEL_ROOT \
    --type directory \
    --exclude public \
    --exclude storage \
    --exclude bootstrap/cache \
    --exec chmod 755

# Apply migration
php artisan migrate --force

# Cache config, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Create the storage symlink in case doesn't exist
php artisan storage:link || true
