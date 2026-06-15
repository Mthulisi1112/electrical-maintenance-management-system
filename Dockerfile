# ---------- Stage 1: Frontend ----------
FROM node:20-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# ---------- Stage 2: Vendor ----------
FROM php:8.3-cli-alpine AS vendor
WORKDIR /app

RUN apk add --no-cache \
    git unzip curl bash \
    libpng-dev libjpeg-turbo-dev freetype-dev libwebp-dev \
    zlib-dev sqlite-dev oniguruma-dev $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_sqlite

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev --optimize-autoloader --no-interaction --no-scripts

COPY . .


# ---------- Stage 3: Runtime ----------
FROM php:8.3-cli-alpine

WORKDIR /app

RUN apk add --no-cache \
    bash curl git sqlite sqlite-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev libwebp-dev \
    zlib-dev oniguruma-dev $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_sqlite

COPY --from=vendor /app /app
COPY --from=frontend /app/public/build /app/public/build

# ONLY minimal permissions (no DB creation here)
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# IMPORTANT: use Railway PORT correctly
CMD sh -c "php -S 0.0.0.0:$PORT -t public"