#!/bin/bash
# Script de despliegue a producción
# Ejecutar en el servidor con: bash deploy.sh

echo "=== Instalando dependencias de PHP ==="
composer install --no-dev --optimize-autoloader

echo "=== Compilando CSS y JavaScript ==="
npm install
npm run build

echo "=== Configurando Laravel ==="
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Creando base de datos ==="
php artisan migrate --force
php artisan db:seed --force

echo "=== Permisos de carpetas ==="
chmod -R 775 storage
chmod -R 775 bootstrap/cache
php artisan storage:link

echo "=== Listo! Sistema desplegado correctamente ==="
