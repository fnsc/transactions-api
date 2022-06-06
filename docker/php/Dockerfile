FROM php:8.1-fpm

WORKDIR /var/www/html

USER root:root

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && apt-get install git vim zip unzip \
    libicu-dev libssl-dev curl -y \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl bcmath pdo_mysql \
    && apt-get clean

RUN pecl install xdebug mongodb \
    && docker-php-ext-enable xdebug mongodb \
    && rm -rf /tmp/*

ARG UID=1000
ARG GID=1000

RUN groupmod -g ${GID} www-data \
    && usermod -u ${UID} -g www-data www-data \
    && chown -hR www-data:www-data \
        /var/www \
        /usr/local/

COPY ./docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 9000

USER www-data:www-data
