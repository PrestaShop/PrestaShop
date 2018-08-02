#!/bin/sh

if [ "$DB_SERVER" = "<to be defined>" -a $PS_INSTALL_AUTO = 1 ]; then
  echo >&2 'error: You requested automatic PrestaShop installation but MySQL server address is not provided '
  echo >&2 '  You need to specify DB_SERVER in order to proceed'
  exit 1
fi

# init if empty
cp -n -R /tmp/data-ps/prestashop/* /var/www/html

if [ -f /var/www/html/config/settings.inc.php -o -f /var/www/html/app/config/parameters.php ]; then
  echo "\n* Remove PrestaShop configuraiton files...";
  rm -f /var/www/html/config/settings.inc.php
  rm -f /var/www/html/app/config/parameters.php
fi

if [ $PS_DEV_MODE -ne 0 ]; then
  echo "\n* Enabling DEV mode ...";
  sed -ie "s/define('_PS_MODE_DEV_', false);/define('_PS_MODE_DEV_',\ true);/g" /var/www/html/config/defines.inc.php
fi

if [ $PS_HOST_MODE -ne 0 -a ! $(grep "define('_PS_HOST_MODE_', true);" /var/www/html/config/defines.inc.php) ]; then
  echo "\n* Enabling HOST mode ...";
  echo "define('_PS_HOST_MODE_', true);" >> /var/www/html/config/defines.inc.php
fi

if [ "$PS_FOLDER_INSTALL" != "install" -a -d /var/www/html/install ]; then
  echo "\n* Renaming install folder as $PS_FOLDER_INSTALL ...";
  mv /var/www/html/install /var/www/html/$PS_FOLDER_INSTALL/
fi

if [ "$PS_FOLDER_ADMIN" != "admin" -a -d /var/www/html/admin ]; then
  echo "\n* Renaming admin folder as $PS_FOLDER_ADMIN ...";
  mv /var/www/html/admin /var/www/html/$PS_FOLDER_ADMIN/
fi

if [ $PS_HANDLE_DYNAMIC_DOMAIN = 1 ]; then
  cp /tmp/docker_updt_ps_domains.php /var/www/html
  sed -ie "s/DirectoryIndex\ index.php\ index.html/DirectoryIndex\ docker_updt_ps_domains.php\ index.php\ index.html/g" $APACHE_CONFDIR/conf-available/docker-php.conf
fi

if [ $PS_INSTALL_AUTO = 1 ]; then
  RET=1
  while [ $RET -ne 0 ]; do
    mysql -h $DB_SERVER -P $DB_PORT -u $DB_USER -p$DB_PASSWD -e "status" > /dev/null 2>&1
    RET=$?
    if [ $RET -ne 0 ]; then
      echo "\n* Waiting for confirmation of MySQL service startup";
      sleep 5
    fi
  done

  echo "\n* Installing PrestaShop, this may take a while ...";
  if [ $PS_ERASE_DB = 1 ]; then
    echo "\n* Drop & recreate mysql database...";
    if [ $DB_PASSWD = "" ]; then
      mysqladmin -h $DB_SERVER -P $DB_PORT -u $DB_USER drop $DB_NAME --force
      mysqladmin -h $DB_SERVER -P $DB_PORT -u $DB_USER create $DB_NAME --force
    else
      mysqladmin -h $DB_SERVER -P $DB_PORT -u $DB_USER -p$DB_PASSWD drop $DB_NAME --force
      mysqladmin -h $DB_SERVER -P $DB_PORT -u $DB_USER -p$DB_PASSWD create $DB_NAME --force
    fi
  fi

  if [ "$PS_DOMAIN" = "<to be defined>" ]; then
    export PS_DOMAIN=$(hostname -i)
  fi

  echo "\n* Install PrestaShop...";
  php /var/www/html/$PS_FOLDER_INSTALL/index_cli.php --domain="$PS_DOMAIN" --db_server=$DB_SERVER:$DB_PORT --db_name="$DB_NAME" --db_user=$DB_USER \
      --db_password=$DB_PASSWD --prefix="$DB_PREFIX" --firstname="John" --lastname="Doe" \
      --password=$ADMIN_PASSWD --email="$ADMIN_MAIL" --language=$PS_LANGUAGE --country=$PS_COUNTRY \
      --newsletter=0 --send_email=0
fi

chown -hR www-data:www-data /var/www/html

echo "\n* Almost ! Starting web server now\n";
exec apache2-foreground
