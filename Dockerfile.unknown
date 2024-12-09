# Base image
FROM php:7.4-apache

# Install Redis PHP extension and dependencies
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    unzip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Copy application code into the container
COPY ./src /var/www/html

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

