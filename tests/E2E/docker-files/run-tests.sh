#!/bin/sh

displayVolumeError () {
  echo "This could be caused by a volume mounted on the root web folder, or the E2E folder."
  echo "1- Check your mounted folder has PrestaShop with the subfolder tests/E2E."
  echo "2- Make sure you ran npm install in the subfolder."
}

if [ ! -d /var/www/html/tests/E2E ]; then
  echo "E2E folder does not exist. Exiting."
  displayVolumeError
  exit 1;
fi

service mysql start
service apache2 start

chmod a+w -R /var/www/html/tests/E2E

if [ ! -d /var/www/html/tests/E2E/node_modules ]; then
  echo "node_modules is missing. Re-executing warmup"
  bash /var/www/html/tests/E2E/docker-files/npm-warmup.sh
fi

su -c "cd /var/www/html/tests/E2E ; npm run start-selenium &> /dev/null" - myuser &

sleep 5
su -c "cd /var/www/html/tests/E2E ; npm run sanity-check" - myuser

if [ $? -ne 0 ]; then
    echo "Could not assert selenium is running. Exiting."
    exit 1;
fi

su -c "cd /var/www/html/tests/E2E ; npm run full-test -- --HEADLESS --MODULE=ps_legalcompliance" - myuser
