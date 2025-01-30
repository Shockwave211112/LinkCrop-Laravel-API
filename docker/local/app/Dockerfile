FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y \
        libpq-dev  \
        build-essential \
        locales \
        zip \
        unzip \
        git \
        curl\
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

WORKDIR /var/www/yazu_links
