#!/bin/sh
set -e

# Recreate CI4 writable folder structure if it doesn't exist
# This is necessary because these folders are often ignored by .dockerignore
mkdir -p /var/www/html/writable/cache
mkdir -p /var/www/html/writable/logs
mkdir -p /var/www/html/writable/session
mkdir -p /var/www/html/writable/debugbar

# Set permissions for the web user
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

# Execute the original entrypoint command
exec "$@"
