Integration survival tests
==========================

## What is it ?

Survival tests are "smoke" tests: they perform very simple actions and check nothing is broken.

These tests use the testing framework phpunit.

They boot a shop (see `bootstrap-survival.php` for setting up a shop)
then modify a few Symfony services in order to control their behavior: the services are replaced by mocks
(see `LegacyTests\Integration\PrestaShopBundle\Test\LightWebTestCase`).

## Run

Run tests using phpunit binary using the phpunit.xml configuration file

```
# from the PrestaShop root folder
php -d date.timezone=UTC ./vendor/bin/phpunit -c tests/Integration/Survival/phpunit.xml
```

## What is being tested

These tests take a list of backoffice pages, perform a GET request to them
and check the returned response code is 200.
