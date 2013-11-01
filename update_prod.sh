#! /bin/sh

git pull origin master
php composer.phar update

chmod -R 0777 app/cache
chmod -R 0777 app/logs

sudo -u www-data php app/console doctrine:database:drop --force
sudo -u www-data php app/console doctrine:database:create
sudo -u www-data php app/console doctrine:schema:update --force

sudo -u www-data php app/console assets:install web --symlink
sudo -u www-data php app/console fos:js-routing:dump

sudo -u www-data php app/console fos:user:create admin admin@sharimg.com admin123 --super-admin

echo "Clear images"
rm -rf web/images/*
mkdir web/images/min

sudo -u www-data php app/console cache:clear --env=prod --no-warmup
sudo -u www-data php app/console cache:warmup --env=prod --no-debug
chmod -R 0777 app/cache
chmod -R 0777 app/logs
chown -R www-data:www-data *
