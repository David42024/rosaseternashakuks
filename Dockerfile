# ============================================
# ETAPA 1: Base PHP-FPM
# ============================================
FROM php:8.4-fpm AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    zip unzip git curl libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql mbstring exif pcntl bcmath zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

# ============================================
# ETAPA 2: Dependencias PHP
# ============================================
FROM base AS dependencies
COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# ============================================
# ETAPA 3: Build Frontend (Node.js)
# ============================================
FROM node:20-alpine AS frontend

WORKDIR /var/www/html

# 1. Copiar archivos de configuración de paquetes y herramientas
COPY package*.json ./
COPY vite.config.js ./ 
COPY tailwind.config.js ./ 
COPY postcss.config.js ./ 

# 2. Instalar dependencias (incluyendo devDependencies para Vite)
# Usamos npm ci para ser estrictos con package-lock.json, pero si falla, fallback a install
RUN npm ci || npm install

# 3. Copiar SOLO los recursos necesarios para el build (JS/CSS)
# Esto evita copiar todo el proyecto PHP a la imagen de Node
COPY resources/ ./resources/

# 4. Ejecutar el build
# Si falla por falta de archivos, Vite lo dirá claramente ahora
RUN npm run build

# ============================================
# ETAPA 4: Imagen Final
# ============================================
FROM base AS final

# Copiar vendor PHP
COPY --from=dependencies /var/www/html/vendor ./vendor

# Copiar TODO el código fuente del proyecto
COPY . .

# Sobrescribir la carpeta public con los assets compilados
COPY --from=frontend /var/www/html/public ./public

# Permisos
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache public

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER www-data
EXPOSE 9000

CMD ["/entrypoint.sh"]