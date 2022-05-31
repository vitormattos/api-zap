#!/bin/bash
. `pwd`/../.env

if [ ! -d ".git" ]; then
    composer install
    vendor/bin/phinx migrage
fi;

php -S 0.0.0.0:80 -t /var/www/public