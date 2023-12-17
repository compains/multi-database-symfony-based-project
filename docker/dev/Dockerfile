FROM php:8.2.0-apache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    apt-get install -y libzip-dev \
    openssl \
    git \
    unzip \
    libpng-dev \
    acl

RUN docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install sockets && \
    docker-php-ext-install pcntl && \
    docker-php-ext-install zip

RUN docker-php-ext-enable pdo pdo_mysql

RUN a2enmod rewrite

WORKDIR /var/www/html

RUN a2enmod rewrite
