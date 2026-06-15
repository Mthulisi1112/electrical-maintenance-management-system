# syntax=docker/dockerfile:1

# ---------- Stage 1: Frontend ----------
FROM node:20-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# ---------- Stage 2: Composer ----------
FROM php:8.3-cli-alpine AS vendor
WORKDIR /app

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git unzip curl bash \
    libpng-dev libjpeg-turbo-dev freetype-dev libwebp-dev \
    zlib-dev sqlite-dev oniguruma-dev $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_sqlite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Copy full application source
COPY . .

# Install dependencies – let Composer run its scripts (important!)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# ---------- Stage 3: Runtime ----------
FROM php:8.3-cli-alpine

WORKDIR /var/www/html

# Install runtime system dependencies (same extensions as vendor stage)
RUN apk add --no-cache \
    bash curl git sqlite sqlite-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev libwebp-dev \
    zlib-dev oniguruma-dev $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_sqlite

# Copy application code
COPY . /var/www/html

# Copy vendor and frontend build artifacts
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=frontend /app/public/build /var/www/html/public/build

# Create necessary directories and set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Prepare SQLite database (if using default path)
RUN touch database/database.sqlite && chmod 666 database/database.sqlite

# Generate app key if not already provided via environment
# Also run package discovery, config/route caching
RUN php artisan package:discover --ansi \
    && php artisan key:generate --no-interaction --ansi \
    && php artisan config:cache --no-interaction --ansi \
    && php artisan route:cache --no-interaction --ansi

EXPOSE 8080

# Use the built-in PHP server – Railway sets $PORT
CMD sh -c "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"