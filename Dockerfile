ARG PHP_VERSION=8.5

FROM composer:2.10.2 AS composer

FROM php:${PHP_VERSION}-cli-alpine

WORKDIR /opt/code

COPY --from=composer /usr/bin/composer /usr/bin/composer
