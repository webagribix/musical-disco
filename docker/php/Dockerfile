FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev libzip-dev libonig-dev unzip curl git \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 9000
CMD ["php-fpm"]
