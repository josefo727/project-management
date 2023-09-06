#!/bin/bash

set -e

pkill -f "php artisan queue:work" || true
pkill -f "php artisan schedule:work" || true

php artisan down

rm -rf vendor node_modules

composer install --no-interaction --optimize-autoloader --no-dev

if grep -q "APP_KEY=base64:" .env; then
    echo "APP_KEY ya está configurada, no se generará una nueva"
else
    echo "Generando nueva APP_KEY..."
    php artisan key:generate
fi

php artisan migrate
php artisan optimize:clear
if [ ! -L public/storage ]; then
    php artisan storage:link
else
    echo "El enlace simbólico ya existe. No se ejecutó el comando."
fi

npm install
npm run build

chown -R application:application .

php artisan up

php artisan queue:work >/dev/null 2>&1 &
php artisan schedule:work >/dev/null 2>&1 &
