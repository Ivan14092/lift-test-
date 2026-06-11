FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libssl-dev pkg-config \
    && pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis \
    && curl -sS https://getcomposer.org/installer | php -- \
       --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html