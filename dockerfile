# Usamos PHP 8.2 FPM como base
FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias para Laravel y extensiones PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www/html

# Copiar todo el proyecto al contenedor
COPY . .

# Instalar dependencias de PHP con Composer
RUN composer install --no-interaction --optimize-autoloader

# Dar permisos necesarios a storage y bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer puerto PHP-FPM
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]
