FROM php:8.2-cli

# Instalar dependencias del sistema y extensiones necesarias
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar código (en docker-compose haremos bind mount, así que esto no es crítico)
COPY . .

# Instalar dependencias de Laravel (esto se hace en el build inicial, luego se usa `composer install` en volumen)
RUN composer install --no-dev --optimize-autoloader || true

# Exponer puerto (para Artisan serve)
EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
