FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    nodejs \
    npm \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --prefer-dist \
    && npm install \
    && npm run build
CMD ["whoami"]
RUN chown -R 33:33 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

RUN chown -R 33:33 /var/www/html/vendor \
    && chmod -R 775 /var/www/html/vendor

RUN npm run dev

CMD ["php-fpm"]

