@echo off
echo =============================
echo  SUBIENDO CAMBIOS AL SERVIDOR
echo =============================

cd C:\laragon\www\restaurante

echo.
echo [1/3] Subiendo a GitHub...
git add .
git commit -m "actualizacion"
git push origin main

echo.
echo [2/3] Conectando al servidor...
ssh root@159.223.112.223 "cd /var/www/restaurante && git pull && npm run build && php artisan config:cache && php artisan route:cache"

echo.
echo [3/3] Abriendo el navegador...
start http://159.223.112.223

echo.
echo LISTO! Revisa el navegador.
echo =============================
pause