#!/bin/bash
set -e

service ssh start

ln -sf /var/www/html/storage/uploads /var/www/html/wp-content/uploads

php-fpm -D

exec nginx -g 'daemon off;'