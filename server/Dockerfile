ARG PHP_VERSION=7.4.7
ARG NGINX_VERSION=1.19

#Configure Local Dev TLS
FROM alpine AS dev-certs
WORKDIR /certs
RUN set -ex \
    && wget -q -O mkcert https://github.com/FiloSottile/mkcert/releases/download/v1.4.1/mkcert-v1.4.1-linux-amd64 \
    && chmod +x mkcert \
    && ./mkcert -install \
    && ./mkcert localhost; \
    chmod a+r localhost*

VOLUME /certs


FROM php:${PHP_VERSION}-fpm-buster AS server_php
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# Install PHP dependencies
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/server-php.prod.ini $PHP_INI_DIR/conf.d/server.ini
COPY docker/php/conf.d/www.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

ARG APP_ENV=prod

COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
	composer install --prefer-dist --no-dev --no-scripts --no-progress --no-suggest; \
	composer clear-cache

# do not use .env files in production
COPY .env ./
RUN composer dump-env prod; \
	rm .env

# copy only specifically what we need
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /var/www/html


COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm", "-F"]


# Configure Nginx
FROM nginx:${NGINX_VERSION} as server_nginx

COPY ./docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
WORKDIR /var/www/html/public

COPY --from=server_php /var/www/html/public ./

