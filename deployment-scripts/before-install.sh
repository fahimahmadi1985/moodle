#!/bin/bash

sudo -u www-data php8.2 /var/www/html/moodle/admin/cli/maintenance.php --enable
sudo apt-get update
sudo makdir moodle-vPlus
sudo cp /var/www/html/moodle/config.php /var/www/html/config.php