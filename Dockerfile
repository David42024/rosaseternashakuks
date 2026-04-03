FROM php:8.4-apache

# ============================================
# INSTALAR DEPENDENCIAS DEL SISTEMA
# ============================================
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# ============================================
# INSTALAR COMPOSER
# ============================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ============================================
# CONFIGURAR APACHE PARA LARAVEL
# ============================================
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# ============================================
# COPIAR ARCHIVOS BASE
# ============================================
COPY composer.json composer.lock* ./
COPY .htaccess ./
COPY .env.example .env

# ============================================
# INSTALAR DEPENDENCIAS PHP
# ============================================
RUN composer install --no-interaction --prefer-dist --optimize-autoloader || true

# ============================================
# COPIAR EL RESTO DEL PROYECTO
# ============================================
COPY . .

# ============================================
# INSTALAR FRONTEND
# ============================================
RUN npm install && npm run build || true

# ============================================
# PERMISOS LARAVEL
# ============================================
RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ============================================
# ENTRYPOINT
# ============================================
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# ============================================
# EXPONER PUERTO
# ============================================
EXPOSE 80

CMD ["/entrypoint.sh"]