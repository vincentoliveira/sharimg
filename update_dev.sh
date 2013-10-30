#! /bin/sh

git pull origin develop
php composser.phar update

sudo -u www-data php app/console doctrine:database:drop --force
sudo -u www-data php app/console doctrine:database:create
sudo -u www-data php app/console doctrine:schema:update --force

sudo -u www-data php app/console assets:install web --symlink
sudo -u www-data php app/console fos:js-routing:dump

echo "Clear images"
rm -f web/images/*

chmod -R 0777 app/cache
chmod -R 0777 app/logs
sudo -u www-data php app/console cache:clear --no-warmup
sudo -u www-data php app/console cache:clear --env=test --no-warmup
sudo -u www-data php app/console cache:clear --env=prod --no-warmup
php app/console cache:warmup --env=prod --no-debug
chmod -R 0777 app/cache
chmod -R 0777 app/logs
chown -R www-data:www-data *
