# syntax=docker/dockerfile:1

# ---------- Stage 1: Frontend ----------
FROM node:20-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# ---------- Stage 2: PHP Dependencies ----------
FROM php:8.3-cli-alpine AS vendor
WORKDIR /app

RUN apk add --no-cache \
    git \
    unzip \
    curl

COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev --optimize-autoloader --no-interaction


# ---------- Stage 3: Runtime ----------
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# System dependencies + PHP extensions (FIXED GD)
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
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_sqlite

# Copy app
COPY . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=frontend /app/public/build /var/www/html/public/build

# Laravel permissions
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Nginx config
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

# Supervisor
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

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisor.d/laravel.ini"]