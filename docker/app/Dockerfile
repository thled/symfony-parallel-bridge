FROM php:8.0-fpm-alpine3.12

ENV PHPSTAN_PRO_WEB_PORT=11111

RUN apk add --no-cache \
    icu-dev libzip-dev libxml2-dev \
    bash wget git autoconf gcc g++ make \
    supervisor gnupg \
    wkhtmltopdf ttf-ubuntu-font-family \
    fcgi \
    && docker-php-ext-install pdo_mysql zip intl soap opcache

# Enable php fpm status page
RUN set -xe && echo "pm.status_path = /status" >> /usr/local/etc/php-fpm.d/zz-docker.conf

RUN pecl install xdebug-3.0.1 pcov \
    && docker-php-ext-enable xdebug pcov \
    && chown -R 1000:1000 /usr/local/etc/php/conf.d \
    && echo "" | tee /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini

RUN mkdir -p /.composer && chown 1000 /.composer \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./docker/app/.bash_history /.bash_history
RUN chmod 777 /.bash_history \
    && export HISTFILE=/.bash_history

RUN wget https://github.com/infection/infection/releases/download/0.20.0/infection.phar \
    && chmod +x infection.phar \
    && mv infection.phar /usr/local/bin/infection

WORKDIR /usr/src/app
