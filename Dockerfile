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

RUN apk add --no-cache \
    git unzip curl bash \
    libpng-dev libjpeg-turbo-dev freetype-dev libwebp-dev \
    libpq-dev \
    zlib-dev oniguruma-dev $PHPIZE_DEPS

RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

COPY . .

# Install ALL dependencies (including dev) for seeding
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

COPY --from=frontend /app/public/build /var/www/html/public/build

RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN rm -f database/database.sqlite || true

ENV PORT=8080
EXPOSE 8080

# Keep the --force flag to skip confirmation
CMD ["sh", "-c", "php artisan migrate:fresh --seed --force && php -S 0.0.0.0:${PORT:-8080} -t public"]