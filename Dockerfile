# ============================================
# ETAPA 1: Construcción de dependencias
# ============================================
FROM php:8.4-fpm AS base

# Instalar dependencias del sistema en una sola capa
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

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ============================================
# ETAPA 2: Instalar dependencias PHP
# ============================================
FROM base AS dependencies

COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# ============================================
# ETAPA 3: Build del frontend (opcional)
# ============================================
FROM node:20-alpine AS frontend

WORKDIR /var/www/html
COPY package*.json ./
RUN npm ci --only=production && npm run build || true

COPY . .

# ============================================
# ETAPA 4: Imagen final mínima
# ============================================
FROM base AS final

# Copiar solo lo necesario de las etapas anteriores
COPY --from=dependencies /var/www/html/vendor ./vendor
COPY --from=frontend /var/www/html/public/build ./public/build

# Copiar el código fuente (sin vendor ni node_modules)
COPY . .

# Crear directorios y permisos (sin chmod 777 por seguridad)
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint para tareas de inicialización
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER www-data
EXPOSE 9000

CMD ["/entrypoint.sh"]