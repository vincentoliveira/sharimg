#! /bin/sh

sudo -u www-data php app/console doctrine:database:drop --force --env=test
sudo -u www-data php app/console doctrine:database:create --env=test
sudo -u www-data php app/console doctrine:schema:update --force --env=test

# Add users
sudo -u www-data php app/console fos:user:create test test@sharimg.com test123 --env=test
sudo -u www-data php app/console fos:user:create admin admin@sharimg.com admin123 --super-admin --env=test


bin/behat @SharimgDefaultBundle --no-paths
bin/behat @SharimgContentBundle --no-paths
bin/behat @SharimgUserBundle --no-paths
bin/behat @SharimgImportBundle --no-paths
bin/behat @SharimgAnalyticsBundle --no-paths
