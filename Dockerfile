FROM php:8.2-apache

# Install required system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql gd zip

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure PHP runtime settings
RUN { \
        echo "upload_max_filesize = 25M"; \
        echo "post_max_size = 30M"; \
        echo "memory_limit = 256M"; \
        echo "max_execution_time = 300"; \
        echo "date.timezone = Asia/Kolkata"; \
        echo "display_errors = Off"; \
        echo "log_errors = On"; \
        echo "error_log = /var/log/apache2/php_errors.log"; \
    } > /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY shopping/ /var/www/html/

# Set file permissions for web server and uploads
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/admin/productimages \
    && chmod -R 775 /var/www/html/admin/productimages

EXPOSE 80

CMD ["apache2-foreground"]
