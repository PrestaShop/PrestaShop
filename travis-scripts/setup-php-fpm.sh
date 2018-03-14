#!/bin/bash

echo "* Preparing PHP-FPM ...";

phpenv config-rm xdebug.ini

# Using default configs
cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf
if [ -f ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.d/www.conf.default ]; then
    cp -n ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.d/www.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.d/www.conf;
fi

# Logging
sudo touch /var/log/php-fpm.log && sudo chmod 777 /var/log/php-fpm.log
sudo sed -e "s?;error_log = log/php-fpm.log?error_log = /var/log/php-fpm.log?g" --in-place ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf

# Additionnal configuration
echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
echo "always_populate_raw_post_data = -1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
echo "error_log = /var/log/php-fpm.log" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
echo "memory_limit = 2G" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini

# Starting PHP FPM
~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm
