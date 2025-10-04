FROM php:8.2-cli

# Paquetes del sistema + extensiones PHP
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev libicu-dev libonig-dev libxml2-dev \
 && docker-php-ext-install pdo pdo_mysql intl zip \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Node 20 para Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs \
 && npm -v && node -v

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Rutas necesarias
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# INSTALAR deps y COMPILAR assets (esto genera public/build)
RUN composer install --no-dev --prefer-dist --optimize-autoloader \
 && npm ci --no-audit --no-fund \
 && npm run build

# Arranque: .env si falta, key, migraciones, cachés y server
CMD ["sh","-lc","php -r \"file_exists('.env') || copy('.env.example','.env');\" \
&& php artisan key:generate --force || true \
&& php artisan optimize:clear || true \
&& php artisan migrate --seed --force || true \
&& php artisan config:cache && php artisan route:cache && php artisan view:cache \
&& php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
