FROM php:8.2-apache

# System libraries needed by the PHP extensions below / Composer
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        unzip \
        git \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions required by Symfony + Doctrine MariaDB
RUN docker-php-ext-install \
        pdo_mysql \
        intl \
        zip \
        opcache

# Apache vhost: serve Symfony's public/ and route all unknown paths to index.php
# (there is no public/.htaccess in this project, so FallbackResource replaces it).
RUN printf '%s\n' \
        '<VirtualHost *:80>' \
        '    ServerName localhost' \
        '    DocumentRoot /var/www/html/public' \
        '    <Directory /var/www/html/public>' \
        '        Require all granted' \
        '        FallbackResource /index.php' \
        '    </Directory>' \
        '</VirtualHost>' \
    > /etc/apache2/sites-available/000-default.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
