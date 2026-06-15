# ---------- Frontend ----------
FROM node:20-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# ---------- PHP + Composer ----------
FROM php:8.3-cli-alpine AS app

WORKDIR /app

# Install system deps
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

COPY . .

# Install Laravel deps
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Copy frontend build
COPY --from=frontend /app/public/build /app/public/build

# Permissions
RUN mkdir -p storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

# Create sqlite file (IMPORTANT)
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite

# Railway port fix (VERY IMPORTANT)
ENV PORT=8080

EXPOSE 8080

# START SERVER
CMD ["sh", "-c", "cd public && php -S 0.0.0.0:${PORT:-8080}"]