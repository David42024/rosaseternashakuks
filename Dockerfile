FROM php:8.4-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_pgsql mysqli mbstring exif pcntl bcmath \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar Apache para Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# COPIAR ARCHIVOS DE CONFIGURACIÓN PRIMERO
COPY composer.json composer.lock* .env* ./

# CREAR .env SI NO EXISTE
RUN if [ ! -f .env ]; then \
    echo "APP_NAME=Laravel" > .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=http://localhost" >> .env && \
    echo "LOG_CHANNEL=stack" >> .env && \
    echo "DB_CONNECTION=sqlite" >> .env && \
    echo "CACHE_DRIVER=file" >> .env && \
    echo "SESSION_DRIVER=file" >> .env && \
    echo "QUEUE_CONNECTION=sync" >> .env; \
    fi

# INSTALAR DEPENDENCIAS SIN SCRIPTS POST-INSTALL-CMD
RUN COMPOSER_DISABLE_EVENTS=1 composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# COPIAR EL RESTO DEL PROYECTO
COPY . .

# GENERAR APP_KEY SI ES NECESARIO
RUN php artisan key:generate --show > /tmp/key.txt 2>/dev/null || \
    (php artisan key:generate && echo "Key generated")

# EJECUTAR package:discover MANUALMENTE
RUN php artisan package:discover --ansi || true

# INSTALAR DEPENDENCIAS NODE Y BUILD
RUN npm install && npm run build

# CREAR DIRECTORIOS NECESARIOS
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]