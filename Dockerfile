# Build stage
FROM php:8.2-apache AS builder
LABEL maintainer="Abdoulaye Camara"
LABEL org.opencontainers.image.authors="camaraabdoulayeroo@gmail.com"
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install only necessary extensions for build
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions intl sodium mysqli pdo_mysql zip gd

# Install Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/

# Copy only composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# # Set environment to prod to avoid loading debug bundles
# ENV APP_ENV=prod
# ENV APP_DEBUG=0

# Install dependencies for production
RUN composer install --no-scripts --no-autoloader --optimize-autoloader

# Copy the rest of the application
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Production stage
FROM php:8.2-apache

# Set environment variables for production
# ENV APP_ENV=prod
# ENV APP_DEBUG=0

# Install only production extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions intl sodium mysqli pdo_mysql zip && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Enable Apache modules
RUN a2enmod rewrite headers

# Copy application from builder
WORKDIR /var/www/
COPY --from=builder /var/www/ .

# Copy configuration files
COPY ./docker/php/php-memory-limit.ini /usr/local/etc/php/conf.d/php-memory-limit.ini
COPY ./docker/apache/apache.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/apache/apache-php.conf /etc/apache2/conf-available/

# Activate the PHP configuration
RUN a2enconf apache-php

# Set proper permissions
RUN chown -R www-data:www-data /var/www/ && \
    chmod -R 755 /var/www/public && \
    # Remove unnecessary files
    rm -rf /var/www/var/cache/* && \
    rm -rf /var/www/var/log/* && \
    find /var/www -name ".git*" -exec rm -rf {} +

ENTRYPOINT ["bash", "./docker/docker.sh"]

EXPOSE 80

