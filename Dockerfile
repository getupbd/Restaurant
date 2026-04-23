# Stage 1: Build dependencies
FROM php:8.3-fpm-alpine as vendor

WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    libpng-dev \
    libzip-dev \
    icu-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files and install
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist

# Stage 2: Build frontend assets
FROM node:20-alpine as frontend

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor

RUN npm install && npm run build

# Stage 3: Final production image
FROM php:8.3-fpm-alpine

# Install nginx and other runtime dependencies
RUN apk add --no-cache \
    nginx \
    libpng \
    libzip \
    icu-libs \
    shadow

WORKDIR /var/www/html

# Copy application files
COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=frontend /var/www/html/public/build ./public/build

# Final PHP optimization
RUN composer dump-autoload --optimize --no-dev

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
