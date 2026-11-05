FROM php:8.2-apache

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache modules
RUN a2enmod rewrite headers ssl vhost_alias

# Copy vhost configs
COPY apache/site-a.conf /etc/apache2/sites-available/site-a.conf
COPY apache/site-b.conf /etc/apache2/sites-available/site-b.conf

# Enable vhosts, disable default
RUN a2dissite 000-default.conf && \
    a2ensite site-a.conf site-b.conf

# Copy error pages
COPY apache/403.html /var/www/html/403.html
COPY apache/404.html /var/www/html/404.html
COPY apache/500.html /var/www/html/500.html

EXPOSE 80
