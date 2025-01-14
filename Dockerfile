FROM php:8.3-rc-apache-bookworm

ARG DEBIAN_FRONTEND=noninteractive

# No necesario
# RUN docker-php-ext-install mysqli

# Instalación de PDO
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN apt-get update \
    && apt-get install -y sendmail libpng-dev \
    && apt-get install -y libzip-dev \
    && apt-get install -y zlib1g-dev \
    && apt-get install -y libonig-dev \
    && apt-get install -y libxml2-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN a2enmod rewrite
