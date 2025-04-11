#!/bin/bash
set -e

service ssh start

ln -sf /var/www/html/storage/uploads /var/www/html/wp-content/uploads

exec apache2-foreground