FROM php:8.2-apache

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm \
 && docker-php-ext-install pdo pdo_mysql zip \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

# Apache apunta a /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Copiar proyecto
COPY . .

# Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer install --no-dev --optimize-autoloader

# Build frontend (Vite + Vue + Tailwind)
RUN npm install && npm run build

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
