#!/bin/sh

set -e
# Get vars
WORKSPACE=$1
PHP_VERSION=$2

# Install apache
add-apt-repository ppa:ondrej/php -y
apt update && apt install -y apache2 libapache2-mod-php$PHP_VERSION

# Enable rewrite mode
a2enmod rewrite actions alias ssl

# Disable mpm_event (mpm_prefork should be already loaded)
a2dismod mpm_event

# Copy apache vhost and set Documentroot
cp -f $WORKSPACE/.github/workflows/sanity/apache-vhost /etc/apache2/sites-available/000-default.conf
sed -e "s?%BUILD_DIR%?$(echo $WORKSPACE)?g" --in-place /etc/apache2/sites-available/000-default.conf
sed -e "s?%MKCERT_DIR%?$(echo $WORKSPACE)?g" --in-place /etc/apache2/sites-available/000-default.conf
