# syntax=docker/dockerfile:1

# ---------- Stage 1: Frontend ----------
FROM node:20-alpine AS frontend-builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# ---------- Stage 2: Composer (FIXED) ----------
FROM php:8.3-cli-alpine AS composer-builder
WORKDIR /app

# Install required PHP extensions for Laravel packages (GD FIX INCLUDED)
RUN apk add --no-cache \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_sqlite

COPY composer.json composer.lock ./
RUN php -m && composer install --no-dev --optimize-autoloader --no-interaction


# ---------- Stage 3: Runtime ----------
FROM php:8.3-fpm-alpine AS final

# Install system packages + PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    sqlite \
    sqlite-dev \
    sqlite-libs \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite gd

# ---------------- NGINX ----------------
RUN mkdir -p /etc/nginx/http.d && \
printf '%s\n' \
'server {' \
'    listen 80;' \
'    server_name _;' \
'    root /var/www/html/public;' \
'    index index.php;' \
'' \
'    location / {' \
'        try_files $uri $uri/ /index.php?$query_string;' \
'    }' \
'' \
'    location ~ \.php$ {' \
'        include fastcgi_params;' \
'        fastcgi_pass 127.0.0.1:9000;' \
'        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;' \
'    }' \
'' \
'    location ~ /\. {' \
'        deny all;' \
'    }' \
'}' \
> /etc/nginx/http.d/default.conf


# ---------------- SUPERVISOR ----------------
RUN mkdir -p /etc/supervisor.d && \
printf '%s\n' \
'[supervisord]' \
'nodaemon=true' \
'' \
'[program:php-fpm]' \
'command=php-fpm -F' \
'autostart=true' \
'autorestart=true' \
'' \
'[program:nginx]' \
'command=nginx -g "daemon off;"' \
'autostart=true' \
'autorestart=true' \
> /etc/supervisor.d/laravel.ini


# ---------------- APP ----------------
WORKDIR /var/www/html

COPY --from=composer-builder /app/vendor /var/www/html/vendor
COPY --from=frontend-builder /app/public/build /var/www/html/public/build
COPY . /var/www/html

# Permissions (Laravel fix)
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisor.d/laravel.ini"]