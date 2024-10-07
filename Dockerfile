FROM ghcr.io/roadrunner-server/roadrunner:2024.2.1 AS roadrunner
FROM composer:latest AS composer
FROM php:8.3-cli

WORKDIR /app

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt update && apt install -y unzip libpq-dev && \
        docker-php-ext-install sockets pdo pdo_pgsql pgsql

COPY src .
RUN composer install

CMD rr serve -c .rr.dev.yaml