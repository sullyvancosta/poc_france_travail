#!/bin/sh

mkdir -p /app/data

if [ ! -f "/app/data/app.db" ]; then
    touch /app/data/app.db
    chmod 777 /app/data/app.db
fi

php-fpm -F
