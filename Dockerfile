# Stage 1: Build frontend assets
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# Stage 2: Production image
FROM serversideup/php:8.4-fpm-nginx-alpine

# Switch to root to install dependencies
USER root

# Install PostgreSQL client and PHP extension
RUN apk add --no-cache postgresql-client \
    && install-php-extensions pdo_pgsql

WORKDIR /var/www/html

# Copy composer files first for better caching
COPY --chown=www-data:www-data composer.json composer.lock ./

# Install composer dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application code
COPY --chown=www-data:www-data . .

# Copy built frontend assets
COPY --chown=www-data:www-data --from=frontend /app/public/build ./public/build

# Finish composer install
RUN composer dump-autoload --optimize

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

# Copy init script for S6 (runs before nginx/php-fpm start)
COPY docker/entrypoint.sh /etc/cont-init.d/99-laravel-init
RUN chmod +x /etc/cont-init.d/99-laravel-init

EXPOSE 8080
