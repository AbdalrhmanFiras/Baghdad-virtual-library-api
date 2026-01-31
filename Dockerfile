############################
# 1. Build frontend assets
############################
FROM node:20 AS node_build
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY . .
RUN npm run build

############################
# 2. PHP + Apache
############################
FROM php:8.4-apache

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip \
    libonig-dev libzip-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libicu-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo pdo_mysql zip mbstring exif pcntl bcmath gd intl opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite

# Laravel Apache config (بدون sed)
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
    </Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (مهم: بدون --no-scripts)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Copy application source
COPY . .

# Copy built frontend assets
COPY --from=node_build /app/public/build /var/www/html/public/build

# Permissions
RUN mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]