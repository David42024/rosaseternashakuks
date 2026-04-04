FROM php:8.4-fpm

# Instalar solo extensiones de sistema necesarias para PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql mbstring exif pcntl bcmath zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www/html

# ============================================
# COPIAR ARCHIVOS YA PREPARADOS EN EL HOST
# ============================================
# Como ya ejecutaste composer install y npm run build en el Droplet,
# simplemente copiamos todo el proyecto tal cual está.
COPY . .

# Eliminar archivos innecesarios para producción (opcional, ahorra espacio)
RUN rm -rf node_modules .env.example tests README.md .git .github

# ============================================
# PERMISOS Y CONFIGURACIÓN FINAL
# ============================================
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache public

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER www-data
EXPOSE 9000

CMD ["/entrypoint.sh"]