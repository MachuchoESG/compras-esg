# Usa una imagen base de PHP con FPM
FROM php:8.2-fpm

# Instala extensiones de PHP necesarias
# Instala extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql \
    && apt install dnf


# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia el contenido del proyecto Laravel
COPY . /var/www/html

RUN php artisan config:clear && php artisan config:cache

# Asigna permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN php artisan config:clear && php artisan config:cachev && php artisan route:cache && php artisan view:cache

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Define el directorio de trabajo
WORKDIR /var/www/html

ENTRYPOINT ["sh", "-c", "php artisan config:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && php-fpm"]

