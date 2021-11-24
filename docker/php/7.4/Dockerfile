FROM php:7.4-fpm-alpine

RUN apk add \
	git \
	bash \
	postgresql-dev
	# make \

RUN docker-php-ext-install pgsql pdo_pgsql

RUN php -m

# intl opcache
 	# icu-dev \
 	# && docker-php-ext-configure intl && docker-php-ext-install intl	\
 	# && docker-php-ext-install opcache \

# xdebug
 	# && apk add --no-cache $PHPIZE_DEPS \
  #   && pecl install xdebug \
  #   && docker-php-ext-enable xdebug

# composer
RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/usr/local/bin --filename=composer --quiet

# symfony cli
RUN wget https://get.symfony.com/cli/installer -O - | bash \
	&& mv /root/.symfony/bin/symfony /usr/local/bin/symfony
	# && symfony check:requirements
