ARG PHP_VERSION=7.4.7
ARG NGINX_VERSION=1.19
ARG COMPOSER_VERSION=1.10.10

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

# Building composer
FROM composer:${COMPOSER_VERSION} as composer_build
COPY composer.json symfony.lock /composer_build/
WORKDIR /composer_build
RUN set -eux; \
	composer install --prefer-dist --no-dev --no-scripts --no-progress --no-suggest;


FROM php:${PHP_VERSION}-fpm-buster AS server_php
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*; \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd; \
    mkdir -p /symfony/var/{cache,log}

# build for production
ARG APP_ENV=prod

COPY docker/php/$APP_ENV/php.$APP_ENV.ini $PHP_INI_DIR/conf.d/server.ini
COPY docker/php/$APP_ENV/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /var/www/html

# copy only specifically what we need
COPY bin bin/
COPY config config/
COPY public public/
COPY migrations migrations/
COPY translations tranlations/
COPY templates templates/
COPY translations translations/
COPY src src/
COPY .env .env
COPY --from=composer_build /composer_build/vendor /var/www/html/vendor/
COPY --from=composer_build /composer_build/composer.lock /composer_build/composer.json /composer_build/symfony.lock /var/www/html/
COPY --from=dev-certs /certs /var/www/
COPY .env-example /var/www/html/.env

RUN set -eux; \
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

ARG APP_ENV=dev

COPY docker/nginx/$APP_ENV.conf /etc/nginx/conf.d/default.conf
WORKDIR /var/www/html/public

COPY --from=server_php /var/www/html/public ./

