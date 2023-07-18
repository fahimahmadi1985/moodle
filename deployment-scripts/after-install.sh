#!/bin/bash

sudo apt-get update
sudo -u www-data php8.2 /var/www/html/moodle-vPlus/admin/cli/upgrade.php
sudo cp /var/www/html/config.php /var/www/html/moodle-vPlus
sudo cp /var/www/html/404.html /var/www/html/moodle-vPlus
sudo cp /var/www/html/404.svg /var/www/html/moodle-vPlus
sudo rm -rf /var/www/html/moodle
sudo mv /var/www/html/moodle-vPlus /var/www/html/moodle
sudo chown -R root:root /var/www/html/moodle
sudo chmod -R 755 /var/www/html/moodle
sudo chmod +r /var/www/html/moodle/admin/cli/cron.php
sudo service nginx restart
sudo -u www-data php8.2 /var/www/html/moodle/admin/cli/maintenance.php --disable