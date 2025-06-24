FROM php:8.2-fpm

ARG INSTALL_XDEBUG=false

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Xdebug if requested
RUN if [ "$INSTALL_XDEBUG" = "true" ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug; \
    fi

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy configs
COPY php.ini /usr/local/etc/php/
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www/html
