#!/bin/bash

# Usar el puerto que define la plataforma (Render) o 10000 por defecto
PORT=${PORT:-10000}

# Configurar Apache para usar ese puerto
echo "Listen $PORT" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/*.conf

# Suprimir advertencia de ServerName
echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Ejecutar comandos de Laravel
php artisan config:cache
php artisan route:cache

# Iniciar Apache
exec apache2-foreground