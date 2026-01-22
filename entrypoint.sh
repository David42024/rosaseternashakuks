#!/bin/bash

# Puerto dinámico
PORT=${PORT:-10000}
echo "Listen $PORT" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/*.conf
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configurar permisos
chown -R www-data:www-data /var/www/html 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# 🔗 CREAR SYMLINK DE STORAGE (CLAVE)
php artisan storage:link || true

# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan view:clear

exec apache2-foreground
