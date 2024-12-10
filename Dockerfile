# Usa una imagen base de PHP con FPM
FROM php:8.2-fpm

# Instala extensiones de PHP necesarias
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libssl-dev \
    autoconf \
    gcc \
    make \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql openssl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copia el contenido del proyecto Laravel
COPY . /var/www/html

# Asigna permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ejecutar los comandos de Artisan durante la construcción del contenedor
RUN php artisan config:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache

# Define el directorio de trabajo
WORKDIR /var/www/html

# Comando por defecto para iniciar PHP-FPM
CMD ["php-fpm"]
