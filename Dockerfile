FROM php:8.3-apache

WORKDIR /var/www/html

# =========================
# 1. Install dependencies
# =========================
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip \
    && rm -rf /var/lib/apt/lists/*

# =========================
# 2. Enable required Apache modules
# =========================
RUN a2enmod rewrite

# IMPORTANT: ensure ONLY ONE MPM is enabled (safe method)
RUN apache2ctl -M | grep mpm || true

# Force correct MPM cleanly (no multi-disable chaos)
RUN a2dismod mpm_event || true \
 && a2dismod mpm_worker || true \
 && a2dismod mpm_prefork || true \
 && a2enmod mpm_prefork

# =========================
# 3. Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =========================
# 4. App code
# =========================
COPY . .

RUN composer install --no-dev --optimize-autoloader

# =========================
# 5. Permissions
# =========================
RUN chown -R www-data:www-data /var/www/html

# =========================
# 6. Apache document root (Laravel fix)
# =========================
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# =========================
# 7. Port
# =========================
EXPOSE 80

CMD ["apache2-foreground"]