# Build assets with Node.js
FROM node:20 AS node_build
WORKDIR /app
COPY package.json package-lock.json* ./
# Use npm install because package-lock.json might be missing
RUN npm install
COPY . .
RUN npm run build

# PHP Application
FROM php:8.4-apache

# Set Apache document root to public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Configure Apache to use public directory
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf


# Create custom PHP configuration for increased upload limits
RUN echo "file_uploads = On\n\
memory_limit = 512M\n\
upload_max_filesize = 64M\n\
post_max_size = 64M\n\
max_execution_time = 600\n" > /usr/local/etc/php/conf.d/uploads.ini

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    zip \
    unzip \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    netcat-openbsd \
    iputils-ping \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    zip \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache \
    fileinfo \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-scripts

# Copy application files (Copy from current dir, NOT from node_build yet)
COPY . .

# Copy built assets from node_build stage
COPY --from=node_build /app/public/build /var/www/html/public/build

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Create necessary directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    storage/app/public \
    storage/app/private \
    bootstrap/cache

# Copy and set permissions for entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set proper permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port 80
EXPOSE 80

# Use entrypoint script
ENTRYPOINT ["docker-entrypoint.sh"]

# Start Apache
CMD ["apache2-foreground"]
