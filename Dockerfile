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
    zlib-dev sqlite-dev oniguruma-dev $PHPIZE_DEPS

# PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd pdo pdo_sqlite

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Copy app
COPY . .

# Install dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Frontend build
COPY --from=frontend /app/public/build /var/www/html/public/build

# Ensure Laravel folders exist
RUN mkdir -p storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

# SQLite file (IMPORTANT FIXED PATH)
RUN touch database/database.sqlite

# Railway port
ENV PORT=8080
EXPOSE 8080

# START (FIXED ENTRYPOINT)
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]