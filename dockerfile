# Imagen base PHP + extensiones necesarias
FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear directorio de la app
WORKDIR /var/www

# Copiar archivos
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-interaction --optimize-autoloader

# Permisos para storage y bootstrap/cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Exponer puerto
EXPOSE 8000

# CMD para iniciar php-fpm
CMD ["php-fpm"]
