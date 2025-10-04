FROM php:8.2-cli

# 1) Paquetes del sistema + extensiones PHP
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev libicu-dev libonig-dev libxml2-dev \
 && docker-php-ext-install pdo pdo_mysql intl zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2) Node.js (para compilar assets con Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs \
 && npm -v && node -v

# 3) Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 4) Copia de código (si usas bind mount en local, igual Railway construye desde aquí)
COPY . .

# 5) Rutas necesarias de cache/storage
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# 6) Dependencias y build de frontend
#    (Si no tienes package.json, elimina las líneas de npm)
RUN composer install --no-dev --prefer-dist --optimize-autoloader \
 && ( [ -f package.json ] && npm ci && npm run build || true )

# 7) Arranque: genera key si falta, limpia, migra+seed, cachea y sirve
CMD ["sh","-lc","php artisan key:generate --force || true; php artisan optimize:clear || true; php artisan migrate --seed --force || true; php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
