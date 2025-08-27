#!/bin/sh

LARAVEL_ROOT="/var/www/html"
WEB_USER="www-data"

# Change project ownership
chown -R $DEPLOY_USER:$WEB_USER $LARAVEL_ROOT

# Set directories to 755
find $LARAVEL_ROOT -type d -exec chmod 755 {} \;

# Set files to 644
find $LARAVEL_ROOT -type f -exec chmod 644 {} \;

# Set public, storage, and cache directories group ownership to web server user and writable
chgrp -R $WEB_USER $LARAVEL_ROOT/public $LARAVEL_ROOT/storage $LARAVEL_ROOT/bootstrap/cache
chmod -R ug+rwx $LARAVEL_ROOT/public $LARAVEL_ROOT/storage $LARAVEL_ROOT/bootstrap/cache

# Set the setgid bit so new files inherit group
find $LARAVEL_ROOT/public -type d -exec chmod g+s {} \;
find $LARAVEL_ROOT/storage -type d -exec chmod g+s {} \;
find $LARAVEL_ROOT/bootstrap/cache -type d -exec chmod g+s {} \;

# Apply migration
php artisan migrate --force

# Clear and cache config, routes, views
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache
php artisan event:clear
php artisan event:cache
php artisan optimize

# Create the storage symlink in case doesn't exist
php artisan storage:link
