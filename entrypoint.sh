#!/bin/bash

# ============================================
# CONFIGURAR PUERTO DINÁMICO (Render)
# ============================================
PORT=${PORT:-10000}
echo "Listen $PORT" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/*.conf
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ============================================
# CONFIGURAR PERMISOS
# ============================================
chown -R www-data:www-data /var/www/html 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# ============================================
# LIMPIAR CACHÉ DE LARAVEL
# ============================================
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# ============================================
# GENERAR ARCHIVOS DE CACHÉ (PRODUCCIÓN)
# ============================================
php artisan config:cache
php artisan route:cache

# ============================================
# INICIAR APACHE
# ============================================
exec apache2-foreground