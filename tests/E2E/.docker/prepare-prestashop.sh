#!/bin/sh

if [ "$DB_SERVER" = "<to be defined>" -a $PS_INSTALL_AUTO = 1 ]; then
	echo >&2 'error: You requested automatic PrestaShop installation but MySQL server address is not provided '
	echo >&2 '  You need to specify DB_SERVER in order to proceed'
	exit 1
fi

# init if empty
cp -n -R /tmp/data-ps/prestashop/* /var/www/html

if [ -f /var/www/html/config/settings.inc.php ]; then
  rm /var/www/html/config/settings.inc.php
  echo "Remove unwanted configuration file"
fi

/bin/bash
