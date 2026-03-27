FROM dunglas/frankenphp:1-php8.2

# Instalar dependencias del sistema + postgres
RUN apt-get update && apt-get install -y \
    git unzip zip curl libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /app

# Copiar proyecto
COPY . .

# Instalar dependencias PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permisos Laravel
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 8080

RUN php artisan optimize:clear \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Arranque
CMD ["sh", "-c", "php artisan config:clear && php -S 0.0.0.0:8080 -t public"]