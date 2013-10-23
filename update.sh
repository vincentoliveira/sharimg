#! /bin/sh

git pull
php composer.phar self-update
php composer.phar install

php app/console doctrine:database:create
php app/console doctrine:schema:update --force

php app/console

./cc
