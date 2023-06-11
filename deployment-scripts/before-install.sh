#!/bin/bash

sudo apt-get update
sudo makdir moodle-vPlus
sudo cp /var/www/html/moodle/error/plainpage.php /var/www/html/moodle/index.php
sudo sudo service apache2 restart
