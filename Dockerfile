# Usa una imagen base de PHP con FPM
FROM php:8.2-fpm

# Instala extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia el contenido del proyecto Laravel
COPY . /var/www/html

# Asigna permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Define el directorio de trabajo
WORKDIR /var/www/html

# Copia la configuración de Nginx
COPY ./nginx/laravel.conf /etc/nginx/conf.d/default.conf

# Exponer los puertos necesarios
EXPOSE 80

# Define el comando de inicio
CMD service nginx start && php-fpm
