#!/bin/sh
set -e

php $PS_FOLDER_INSTALL/index_cli.php \
		--db_server=$DB_SERVER:$DB_PORT --db_user=$DB_USER --db_password=$DB_PASSWD --db_name="$DB_NAME" --prefix="$DB_PREFIX" \
		--domain="$PS_DOMAIN" --ssl=$PS_ENABLE_SSL \
		--firstname="$ADMIN_FIRSTNAME" --lastname="$ADMIN_LASTNAME" --email="$ADMIN_MAIL" --password="$ADMIN_PASSWD" \
		--language=$PS_LANGUAGE --country=$PS_COUNTRY --all_languages=$PS_ALL_LANGUAGES \
		--newsletter=0 --send_email=0 --fixtures=$PS_INSTALL_DEMO_PRODUCTS
