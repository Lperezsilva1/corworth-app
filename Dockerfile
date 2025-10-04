FROM php:8.2-cli

# Instalar dependencias del sistema y extensiones necesarias
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libicu-dev libonig-dev libxml2-dev \
 && docker-php-ext-install pdo pdo_mysql intl zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar código
COPY . .

# Crear carpetas de caché que Laravel necesita
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# Instalar dependencias de Laravel (sin ocultar errores)
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Exponer puerto (opcional, Railway usa $PORT)
EXPOSE 8000

# Usar el puerto que Railway inyecta (importante)
CMD ["sh","-lc","php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
