# ---------- Frontend ----------
FROM node:20-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# ---------- PHP + Composer ----------
FROM php:8.3-cli-alpine AS app

WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    git unzip curl bash \
    libpng-dev libjpeg-turbo-dev freetype-dev libwebp-dev \
    libpq-dev \
    zlib-dev oniguruma-dev $PHPIZE_DEPS

# PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_pgsql

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Copy app
COPY . .

# Install dependencies (without running scripts)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Frontend build
COPY --from=frontend /app/public/build /var/www/html/public/build

# Laravel permissions
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Remove SQLite file if present (not needed)
RUN rm -f database/database.sqlite || true

# Railway port
ENV PORT=8080
EXPOSE 8080

# Start server (run migrations & seed first)
CMD ["sh", "-c", "php artisan migrate:fresh --seed && php -S 0.0.0.0:${PORT:-8080} -t public"]