FROM php:7.4-fpm-alpine

RUN apk add \
	bash \
	postgresql-dev

RUN docker-php-ext-install pgsql pdo_pgsql

# composer
RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/usr/local/bin --filename=composer --quiet
