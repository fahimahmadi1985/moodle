#!/bin/bash

sudo apt-get update
sudo cp /var/www/html/config.php /var/www/html/moodle-vPlus
sudo rm -rf /var/www/html/moodle
sudo mv /var/www/html/moodle-vPlus /var/www/html/moodle
sudo sudo service apache2 restart