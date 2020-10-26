#!/bin/bash

echo "Install composer v1.10.16";

 curl -sS https://getcomposer.org/installer | php -- --install-dir=$COMPOSER_BIN_DIR --filename=composer --version=1.10.16

# Restarting Apache
sudo service apache2 restart
