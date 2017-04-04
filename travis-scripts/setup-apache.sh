#!/bin/bash

echo "* Preparing Apache ...";

sudo a2enmod rewrite actions fastcgi alias

# Use default config
sudo cp -f tests/travis-ci-apache-vhost /etc/apache2/sites-available/000-default.conf
sudo sed -e "s?%TRAVIS_BUILD_DIR%?$(pwd)?g" --in-place /etc/apache2/sites-available/000-default.conf
sudo chmod 777 -R $HOME

# Starting Apache
sudo service apache2 restart
