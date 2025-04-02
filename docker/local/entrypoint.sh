#!/bin/bash

echo 'Local Dev Entrypoint started'

mkdir ./bootstrap/cache
mkdir -p ./storage/framework/views
mkdir ./storage/framework/cache
mkdir ./storage/framework/sessions

composer install

php artisan config:cache

php artisan key:generate

php artisan optimize:clear

php artisan optimize

#php artisan migrate:fresh --force
#
#php artisan db:seed --force

chown -R $USER:www-data storage
chown -R $USER:www-data bootstrap/cache
chmod -R 775 bootstrap/cache
chmod -R 775 storage

php artisan queue:work
