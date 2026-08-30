FROM php:8.2-cli

WORKDIR /var/www/html

RUN docker-php-ext-install pdo_mysql

COPY certs/ /usr/local/share/ca-certificates/
RUN update-ca-certificates
