# Prestashop Docker Stack (development use only)

## Run PrestaShop in local

```
cd tools/docker
docker-compose up -d
```

If you do not have composer dependencies, you must:

```
docker exec -it php-apache-prestashop bash
cd /var/www/html
composer install
```

Open your navigator and go to [localhost](http://localhost) or 
[install page](http://localhost/install-dev/) if it's the first time you use it.

### PrestaShop Installation Details

MySQL settings:

**Server address**: mysql-prestashop  
**Database**: prestashop  
**User**: prestashop  
**Password**: password

## Stop containers and volumes

When you want to stop PrestaShop containers, just run:

```
cd tools/docker
docker-compose down -v
```

If you want to stop all containers, like those created by PhpStorm. You can:

```
docker stop $(docker ps -aq) && docker rm $(docker ps -aq) && docker volume prune -f
```

If you want to be sure that all containers have been stopped, run:

```
docker ps -a
```

# Run PrestaShop unit tests

If it's your first time, you'll need to give privileges to the prestashop user  
in MySQL and create the test database.

```
docker exec -it mysql-prestashop bash
mysql -u root -pLetMeIn!
grant all privileges on test_prestashop.* to 'prestashop' identified by 'password';
\q
exit
docker exec -it php-apache-prestashop bash
cd /var/www/html
composer create-test-db
```

Test it with:

```
phpunit -c tests/phpunit.xml tests/Unit/classes/AssetsCoreTest.php
```

##  PHPStorm Settings (2017.3)

Open preferences -> Languages & Frameworks -> PHP -> Test Frameworks

1. Add a PHPUnit by remote interpreter  
2. Choose or add a remote interpreter from Docker with settings:  
**Type**: Docker Compose  
**Server**: Docker  
**Configuration file(s)**: tools/docker/docker-compose-tests.yml  
**Service**: Choose php-apache-tests  
3. When your CLI interpreter is added, select it in the PHPUnit by Remote Interpreter 
dialog box and submit.  
**PHPUnit library**: Use Composer autoloader  
**Path to script**: /var/www/html/vendor/autoload.php  
*In Test runner:*  
**Default configuration file**: /var/www/html/tests/phpunit.xml  
Click on the refresh button, PHPStorm shoud display a PHPUnit version.
4. Try to run a test, PHPStorm shoud ask for a PHP interpreter, click on the fix 
button and select php-apache-tests as value for the CLI Interpreter select element, 
submit.  

You shoud now be able to run PHPUnit tests.

## Misc

### Go into the PHP-Apache container

```
docker exec -it php-apache-prestashop bash
```

### Go into the MySQL container

```
docker exec -it mysql-prestashop bash
```

## Informations

- Database credentials are available in the docker-composer.yml file.
- MySQL data are persisted into tools/docker/data/mysql
- The tools/docker/data/tests/ps_dump.sql file is used by our unit tests files to 
recreate the test database at different times.
- The MySQL container is shared between your docker-compose.yml for local  
development and docker-compose-tests.yml for unit testing.
