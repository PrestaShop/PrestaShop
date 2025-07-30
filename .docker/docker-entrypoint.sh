#!/bin/sh
set -e

# Install vendors according to the current composer.lock file
if [ -z "$(ls -A 'vendor/' 2>/dev/null)" ]; then
	composer install --prefer-dist --no-progress --no-interaction
fi

# Display information about the current project
# Or about an error in project initialization
php bin/console -V

# Wait for database to be ready
echo 'Waiting for database to be ready...'
ATTEMPTS_LEFT_TO_REACH_DATABASE=60
until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(php bin/console dbal:run-sql -q "SELECT 1" 2>&1); do
	if [ $? -eq 255 ]; then
		# If the Doctrine command exits with 255, an unrecoverable error occurred
		ATTEMPTS_LEFT_TO_REACH_DATABASE=0
		break
	fi
	sleep 1
	ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
	echo "Still waiting for database to be ready... Or maybe the database is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left."
done

# If the database is not up or not reachable, exit
if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
	echo 'The database is not up or not reachable:'
	echo "$DATABASE_ERROR"
	exit 1
else
	echo 'The database is now ready and reachable'
fi

# Set permissions for the var folder
setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

# If the parameters.php file is not found, this means it's a fresh install of PrestaShop
if [ ! -f app/config/parameters.php ]; then
	# Build all assets using the existing build script
	chmod +x ./tools/assets/build.sh && ./tools/assets/build.sh all

	if [ "$PS_INSTALL_AUTO" = "true" ]; then
		php $PS_FOLDER_INSTALL/index_cli.php \
			--db_server=$DB_SERVER:$DB_PORT --db_user=$DB_USER --db_password=$DB_PASSWORD --db_name="$DB_NAME" --prefix="$DB_PREFIX" \
			--domain="$PS_DOMAIN" --ssl=$PS_ENABLE_SSL \
			--firstname="$ADMIN_FIRSTNAME" --lastname="$ADMIN_LASTNAME" --email="$ADMIN_MAIL" --password="$ADMIN_PASSWORD" \
			--language=$PS_LANGUAGE --country=$PS_COUNTRY --all_languages=$PS_ALL_LANGUAGES \
			--newsletter=0 --send_email=0 --fixtures=$PS_INSTALL_DEMO_PRODUCTS
	fi
fi

echo "\n***"
echo "**"
echo "** Front-office: http://${PS_DOMAIN}/"
echo "**  Back-office: http://${PS_DOMAIN}/admin-dev"
echo "**   Login with:"
echo "**     username: ${ADMIN_MAIL}"
echo "**     password: ${ADMIN_PASSWD}"
if [ $PS_USE_DOCKER_MAILDEV -eq 1 ]; then
    echo "**"
    echo "** To view sent emails point your browser to http://localhost:1080/"
fi
echo "**"
echo "***\n"

exec docker-php-entrypoint "$@"
