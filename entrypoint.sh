#!/bin/bash
set -e

# Función para esperar a la BD con timeout
wait_for_db() {
    echo "🔄 Esperando a la base de datos..."
    local host="${DB_HOST:-postgres}"
    local port="${DB_PORT:-5432}"
    
    # Intentar conectar máximo 30 veces (30 segundos)
    for i in $(seq 1 30); do
        if nc -z "$host" "$port" 2>/dev/null; then
            echo "✅ Base de datos lista en $host:$port"
            return 0
        fi
        echo "⏳ Intento $i/30: Esperando a $host:$port..."
        sleep 1
    done
    
    echo "❌ No se pudo conectar a la BD después de 30 intentos."
    echo "💡 Verifica: DB_HOST=$host, DB_PORT=$port, y que el servicio 'postgres' esté corriendo."
    # Continuamos sin error para permitir debug (PHP intentará conectar y fallará con mensaje claro)
    return 0
}

# Ejecutar espera
wait_for_db

# Generar .env si no existe (solo para desarrollo)
if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

# Migraciones y cachés (solo si no es producción estricta)
if [ "${APP_ENV:-production}" != "production" ]; then
    php artisan migrate --force 2>/dev/null || true
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
fi

# Iniciar PHP-FPM (esto es lo que Nginx necesita)
exec php-fpm