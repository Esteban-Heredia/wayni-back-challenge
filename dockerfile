# Usamos imagen de PHP + Composer
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
    && rm -rf /var/lib/apt/lists/*

# Copiamos Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecemos el directorio de trabajo
WORKDIR /var/www/html

# Copiamos los archivos de Composer primero para aprovechar cache
COPY composer.json composer.lock ./

# Instalamos dependencias de PHP
RUN composer install --no-interaction --optimize-autoloader

# Ahora copiamos el resto del proyecto
COPY . .

# Si necesitas correr Artisan commands de inicialización
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Exponemos el puerto (opcional, si tu contenedor sirve PHP directamente)
EXPOSE 8000

# CMD final del contenedor
CMD ["php-fpm"]
