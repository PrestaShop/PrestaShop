Integration tests
=================

## Run

Run tests using phpunit binary using the phpunit.xml configuration file

```
# from the PrestaShop root folder
php -d date.timezone=UTC ./vendor/bin/phpunit -c tests/Integration/phpunit.xml
```

## Run behat

Run tests using behat binary using the right behat.yml configuration file

```
# from the PrestaShop root folder
php -d date.timezone=UTC ./vendor/bin/behat -c tests/Integration/Domain/behat.yml
```
