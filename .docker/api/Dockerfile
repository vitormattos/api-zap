FROM php:8.1-fpm
ARG host_uid=1000
ARG host_gid=1000

RUN apt-get update

RUN apt-get install -y \
        procps \
        unzip

RUN docker-php-ext-install sockets

# PostgreSQL
RUN apt install -y libpq-dev
RUN docker-php-ext-install pdo_pgsql

# Install xdebug
RUN pecl install xdebug-3.1.4 \
    && docker-php-ext-enable xdebug

RUN usermod --non-unique --uid ${host_uid} www-data \
  && groupmod --non-unique --gid ${host_gid} www-data

USER www-data

COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV COMPOSER_HOME=/home/composer

WORKDIR /var/www

COPY config/php.ini /usr/local/etc/php/conf.d/
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT [ "bash", "/usr/local/bin/entrypoint.sh" ]