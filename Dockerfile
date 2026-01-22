FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Configurar Apache para Laravel (apuntar a /public)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilitar AllowOverride para .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copiar composer.json primero (para cache de Docker)
COPY composer.json composer.lock ./

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Instalar dependencias PHP
RUN composer install --no-dev --no-scripts --no-autoloader

# Copiar package.json
COPY package.json package-lock.json* ./

# Instalar dependencias Node
RUN npm install

# Copiar el resto del proyecto
COPY . .

# Completar instalación de Composer
RUN composer dump-autoload --optimize

# Build frontend (Vite + Vue + Tailwind)
RUN npm run build

# Crear directorios necesarios y permisos
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Puerto
EXPOSE 8080

# Usar puerto de Fly.io
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

CMD ["apache2-foreground"]