FROM php:8.0-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

# 添加 WordPress 友好的 Apache 配置
COPY apache.conf /etc/apache2/conf-available/custom-wordpress.conf
RUN a2enconf custom-wordpress

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .

EXPOSE 80

CMD ["apache2-foreground"]
