FROM php:8.4-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git curl unzip zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install

EXPOSE 8000