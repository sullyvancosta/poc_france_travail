FROM php:8.4.12-fpm

# Dependencies install
RUN apt update && apt install -y curl git unzip libicu-dev libsqlite3-dev

RUN docker-php-ext-install intl pdo pdo_sqlite

# Composer install
ENV COMPOSER_VERSION=2.8.11
RUN curl -sS https://getcomposer.org/installer | php -- --version=$COMPOSER_VERSION --install-dir=/usr/local/bin --filename=composer

# Manage entrypoint and co
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /app

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
