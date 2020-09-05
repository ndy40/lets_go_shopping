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

FROM composer:${COMPOSER_VERSION} as composer_build

COPY composer.json symfony.lock .env /composer_build/

WORKDIR /composer_build

RUN set -eux; \
	composer install --prefer-dist --no-dev --no-scripts --no-progress --no-suggest --optimize-autoloader;



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
    rm -rf /var/lib/apt/lists/*; \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd; \
    mkdir -p /symfony/var/{cache,log}

#RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/prod/server-php.prod.ini $PHP_INI_DIR/conf.d/server.ini
COPY docker/php/prod/www.conf /usr/local/etc/php-fpm.d/www.conf

ARG APP_ENV=prod

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# copy only specifically what we need
COPY bin bin/
COPY config config/
COPY docker docker/
COPY public public/
COPY src src/
COPY --from=composer_build /composer_build/vendor /symfony/vendor/
COPY --from=composer_build /composer_build/composer.lock /composer_build/composer.json /composer_build/symfony.lock /var/www/html/
COPY .env /var/www/html/
COPY --from=dev-certs /certs/ /var/www/certs/

RUN set -eux; \
    [ -e "/var/www/html/var" ] || ln -s /symfony/var /var/www/html/var;  \
    [ -e "/var/www/html/vendor" ] || ln -s /symfony/vendor /var/www/html/vendor; \
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

COPY docker/nginx/prod.conf /etc/nginx/conf.d/default.conf
WORKDIR /var/www/html/public

COPY --from=server_php /var/www/html/public ./

