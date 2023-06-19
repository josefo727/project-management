#!/bin/bash

rm -rf vendor node_modules
composer install
npm install

linkPath=$(php -r "echo public_path('storage');")

if [ ! -L "$linkPath" ]; then
    # El enlace simbólico no existe, ejecutar el comando php artisan storage:link
    output=$(php artisan storage:link)
    echo "$output"
else
    echo "El enlace simbólico ya existe. No se ejecutó el comando."
fi

php artisan queue:work &
php artisan migrate
php artisan db:seed
npm run build
php artisan optimize:clear
php artisan serve --host 0.0.0.0
