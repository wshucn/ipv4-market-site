#!/bin/bash

#docker build -t ipv4-market-site .

if [ "$(docker ps -a -q -f name=ipv4-wordpress)" ]; then
  echo "stop ipv4-wordpress..."
  docker stop ipv4-wordpress
  docker rm ipv4-wordpress
fi

docker run -d --name ipv4-wordpress \
  -e DB_NAME=ipv4-market-site-dev \
  -e DB_USER=admin \
  -e DB_PASSWORD=HilcoDev \
  -e DB_HOST=ipv4-market-site-dev.chma48owslfg.us-east-1.rds.amazonaws.com \
  -e DB_CHARSET=utf8mb4 \
  -e DB_COLLATE=utf8mb4_unicode_ci \
  -e AUTH_KEY=123456 \
  -e SECURE_AUTH_KEY=123456 \
  -e LOGGED_IN_KEY=123456 \
  -e NONCE_KEY=123456 \
  -e AUTH_SALT=123456 \
  -e SECURE_AUTH_SALT=123456 \
  -e LOGGED_IN_SALT=123456 \
  -e NONCE_SALT=123456 \
  -e WP_DEBUG_LOG=true \
  -p 18080:80 \
  -v $(pwd):/var/www/html \
  ipv4-market-site

echo "http://localhost:18080"


# local development
docker run -d --name ipv4-wordpress \
  -e DB_NAME=pantheon \
  -e DB_USER=root \
  -e DB_PASSWORD=123456 \
  -e DB_HOST=host.docker.internal \
  -e DB_CHARSET=utf8mb4 \
  -e DB_COLLATE=utf8mb4_unicode_ci \
  -e AUTH_KEY=123456 \
  -e SECURE_AUTH_KEY=123456 \
  -e LOGGED_IN_KEY=123456 \
  -e NONCE_KEY=123456 \
  -e AUTH_SALT=123456 \
  -e SECURE_AUTH_SALT=123456 \
  -e LOGGED_IN_SALT=123456 \
  -e NONCE_SALT=123456 \
  -e WP_DEBUG_LOG=true \
  -p 4000:80 \
  -v $(pwd)/wp-content:/var/www/html/wp-content \
  -v $(pwd)/../uploads:/var/www/html/wp-content/uploads \
  ipv4-market-site
