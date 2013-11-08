#! /bin/sh

PULL=$1

if [ ! -z $PULL ]
then
    git pull origin develop
    php composer.phar update
fi

chmod -R 0777 app/cache
chmod -R 0777 app/logs

sudo -u www-data php app/console doctrine:database:drop --force
sudo -u www-data php app/console doctrine:database:create
sudo -u www-data php app/console doctrine:schema:update --force

sudo -u www-data php app/console assets:install web --symlink
sudo -u www-data php app/console fos:js-routing:dump

# Add users
sudo -u www-data php app/console fos:user:create test test@sharimg.com test123
sudo -u www-data php app/console fos:user:create admin admin@sharimg.com admin123 --super-admin

echo "Clear images"
rm -rf web/logs
rm -rf web/images
mkdir web/logs
mkdir web/images
mkdir web/images/min

sudo -u www-data php app/console cache:clear --no-warmup
sudo -u www-data php app/console cache:clear --env=test --no-warmup
sudo -u www-data php app/console cache:clear --env=prod --no-warmup
php app/console cache:warmup --env=prod --no-debug
chmod -R 0777 app/cache
chmod -R 0777 app/logs
chown -R www-data:www-data *
