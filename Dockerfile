# Use PHP 8.2 FPM Alpine as base image
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk update && apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    openssh-client \
    nginx \
    supervisor \
    nano \
    vim \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ \
    && docker-php-ext-install \
    gd \
    bcmath \
    ctype \
    fileinfo \
    mbstring \
    pdo_mysql \
    xml \
    zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Remove default server configuration
RUN rm /etc/nginx/nginx.conf

# Copy nginx and supervisor configuration
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./docker/php.ini /usr/local/etc/php/conf.d/99-custom.ini

# Set permissions
RUN chown -R www-data:www-data .

# Start Nginx and PHP-FPM via Supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Expose port 80
EXPOSE 80
