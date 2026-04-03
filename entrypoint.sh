#!/bin/bash
set -e

# Esperar a que la BD esté lista (si existe variable de conexión)
if [ ! -z "$DB_HOST" ] && [ ! -z "$DB_PORT" ]; then
    echo "🔄 Esperando a la base de datos..."
    while ! nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; do
        sleep 1
    done
fi

# Generar .env si no existe (solo para desarrollo)
if [ ! -f .env ] && [ -f .env.example ]; then
    echo "⚠️  .env no encontrado, copiando desde .env.example"
    cp .env.example .env
fi

# Ejecutar migraciones y optimizaciones (solo si APP_ENV != production)
if [ "$APP_ENV" != "production" ]; then
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Ejecutar PHP-FPM
exec php-fpm