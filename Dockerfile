FROM php:8.4-fpm

# ============================================
# 1. INSTALAR DEPENDENCIAS DEL SISTEMA + NODEJS
# ============================================
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
    # Instalamos Node.js y npm desde el repositorio oficial (más ligero que node:alpine en multi-stage)
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    # Extensiones PHP
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql mbstring exif pcntl bcmath zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ============================================
# 2. COPIAR ARCHIVOS DE DEFINICIÓN
# ============================================
COPY composer.json composer.lock* package*.json ./
COPY vite.config.js tailwind.config.js postcss.config.js ./

# ============================================
# 3. INSTALAR DEPENDENCIAS Y COMPILAR
# ============================================
# Instalamos dependencias de PHP (sin dev)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Instalamos dependencias de Node (CON devDependencies para Vite)
RUN npm ci || npm install

# Copiamos el resto del código (incluyendo resources/)
COPY . .

# Compilamos los assets de frontend
RUN npm run build

# Eliminamos Node.js y npm para reducir el tamaño de la imagen final (opcional pero recomendado)
RUN apt-get purge -y nodejs npm && apt-get autoremove -y && apt-get clean

# ============================================
# 4. PERMISOS Y CONFIGURACIÓN FINAL
# ============================================
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public \
    && chmod -R 775 storage bootstrap/cache public

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER www-data
EXPOSE 9000

CMD ["/entrypoint.sh"]