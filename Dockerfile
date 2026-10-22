FROM php:8.4.27-apache

# Use official precompiled PHP extension installer binary (no compiling from source)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install precompiled PHP extensions & dependencies instantly
RUN install-php-extensions intl mysqli pdo_mysql zip gd

# Enable Apache rewrite and headers modules
RUN a2enmod rewrite headers

# Update Apache configuration for DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html

# Install Composer dependencies (bypass legacy lock requirements for PHP 8.4 runtime)
RUN composer update aws/aws-sdk-php --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs || composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Set permissions for writable directory (create it if not exists)
RUN mkdir -p /var/www/html/writable \
    && chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable


# Copy and set entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
EXPOSE 80
