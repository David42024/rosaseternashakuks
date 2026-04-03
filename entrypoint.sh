#!/bin/bash

set -e

# ============================================
# CONFIGURACIÓN APACHE (PUERTO 80)
# ============================================
PORT=${PORT:-80}
echo "Listen $PORT" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/*.conf
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ============================================
# PERMISOS LARAVEL
# ============================================
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ============================================
# CREAR SYMLINK STORAGE
# ============================================
php artisan storage:link || true

# ============================================
# ESPERAR A POSTGRESQL
# ============================================
if [ -n "$DB_HOST" ]; then
    echo "Esperando conexión a PostgreSQL..."
    until php -r "
        try {
            new PDO('pgsql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_DATABASE', '$DB_USERNAME', '$DB_PASSWORD');
            echo 'Conectado!';
            exit(0);
        } catch(Exception \$e) {
            exit(1);
        }
    " 2>/dev/null; do
        echo "Esperando PostgreSQL..."
        sleep 2
    done
fi

# ============================================
# MIGRACIONES (PRODUCCIÓN)
# ============================================
if [ "$APP_ENV" = "production" ]; then
    php artisan migrate --force || true
fi

# ============================================
# OPTIMIZACIÓN LARAVEL
# ============================================
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ============================================
# INICIAR APACHE
# ============================================
exec apache2-foreground