FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

COPY ./php.ini /usr/local/etc/php/

RUN a2enmod rewrite

WORKDIR /var/www/html/public
