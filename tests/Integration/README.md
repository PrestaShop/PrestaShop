Integration tests
=================

## Run

Run tests using behat binary using the right behat.yml configuration file

```
# from the PrestaShop root folder
php -d date.timezone=UTC ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml
```
