FROM php:8.2-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git unzip libssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-interaction --optimize-autoloader

CMD php artisan serve --host=0.0.0.0 --port=8080
