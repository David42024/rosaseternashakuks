#!/bin/bash

# ============================================
# CONFIGURACIÓN PARA DROPLET - PUERTO ESTÁNDAR
# ============================================
# En droplet usamos puerto 80 estándar, no variable
PORT=${PORT:-80}
echo "Listen $PORT" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/*.conf
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ============================================
# CONFIGURAR PERMISOS CORRECTAMENTE
# ============================================
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/public

# ============================================
# CREAR SYMLINK DE STORAGE
# ============================================
php artisan storage:link || true

# ============================================
# ESPERAR A QUE MYSQL ESTÉ LISTO (OPCIONAL)
# ============================================
if [ -n "$DB_HOST" ]; then
    echo "Esperando conexión a MySQL..."
    until php -r "try { new PDO('mysql:host=$DB_HOST;dbname=$DB_DATABASE', '$DB_USERNAME', '$DB_PASSWORD'); echo 'Conectado!'; exit(0); } catch(Exception \$e) { exit(1); }" 2>/dev/null; do
        echo "Esperando MySQL..."
        sleep 2
    done
fi

# ============================================
# EJECUTAR MIGRACIONES (SOLO EN PRODUCCIÓN)
# ============================================
if [ "$APP_ENV" = "production" ]; then
    php artisan migrate --force || true
fi

# ============================================
# OPTIMIZACIONES PARA PRODUCCIÓN
# ============================================
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ============================================
# INICIAR APACHE
# ============================================
exec apache2-foreground