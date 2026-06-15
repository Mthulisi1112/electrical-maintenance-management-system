# syntax=docker/dockerfile:1

# ---------- Stage 1: Frontend ----------
FROM node:20-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# ---------- Stage 2: Composer + PHP Extensions ----------
FROM php:8.3-cli-alpine AS vendor
WORKDIR /app

# System dependencies (FIXED: includes sqlite-dev for pdo_sqlite)
RUN apk add --no-cache \
    git \
    unzip \
    curl \
    bash \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    zlib-dev \
    sqlite-dev \
    oniguruma-dev \
    $PHPIZE_DEPS

# Install PHP extensions BEFORE composer
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_sqlite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

# Install Laravel dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction


# ---------- Stage 3: Runtime ----------
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Runtime system packages
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    git \
    sqlite \
    sqlite-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    zlib-dev \
    oniguruma-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_sqlite

# Copy application
COPY . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=frontend /app/public/build /var/www/html/public/build

# Laravel permissions fix
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose HTTP port (Railway uses this)
EXPOSE 80

# Start PHP-FPM
CMD ["php-fpm", "-F"]