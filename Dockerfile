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
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring exif pcntl bcmath zip \
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
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# ============================================
# COPIAR ARCHIVOS DE CONFIGURACIÓN PRIMERO
# ============================================
COPY composer.json composer.lock* ./
COPY .htaccess ./

# ============================================
# INSTALAR DEPENDENCIAS PHP
# ============================================
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# ============================================
# COPIAR EL RESTO DEL PROYECTO
# ============================================
COPY . .

# ============================================
# INSTALAR DEPENDENCIAS NODE Y COMPILAR
# ============================================
RUN npm install && npm run build || true

# ============================================
# CREAR DIRECTORIOS Y CONFIGURAR PERMISOS
# ============================================
RUN mkdir -p storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ============================================
# COPIAR ENTRYPOINT
# ============================================
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# ============================================
# EXPONER PUERTO 80
# ============================================
EXPOSE 80

CMD ["/entrypoint.sh"]