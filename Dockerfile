# ============================================
# ETAPA 1: Base PHP-FPM
# ============================================
FROM php:8.4-fpm AS base

# Instalar dependencias del sistema esenciales
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
# ETAPA 2: Dependencias PHP (Composer)
# ============================================
FROM base AS dependencies

COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# ============================================
# ETAPA 3: Build Frontend (Node.js)
# ============================================
FROM node:20-alpine AS frontend

WORKDIR /var/www/html

# Copiar solo archivos de definición para aprovechar caché de Docker
COPY package*.json ./
COPY vite.config.js ./ 
COPY tailwind.config.js ./ 
COPY postcss.config.js ./ 

# ⚠️ IMPORTANTE: Usar 'npm install' o 'npm ci' SIN '--only=production'
# Necesitamos devDependencies para compilar Vite/Tailwind
RUN npm ci && npm run build

# ============================================
# ETAPA 4: Imagen Final (Producción)
# ============================================
FROM base AS final

# 1. Copiar vendor de PHP
COPY --from=dependencies /var/www/html/vendor ./vendor

# 2. Estrategia segura para Assets:
# Copiamos TODO el código fuente primero
COPY . .

# Luego, sobrescribimos la carpeta 'public' con la versión compilada de la etapa frontend
# Esto asegura que tengamos index.php correcto + assets minificados
COPY --from=frontend /var/www/html/public ./public

# 3. Permisos y estructura de directorios
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache public

# 4. Entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER www-data
EXPOSE 9000

CMD ["/entrypoint.sh"]