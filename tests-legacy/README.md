Tests Legacy
============

This folder contains legacy unit and integration tests written for PrestaShop 1.7.0 to 1.7.5.

As the structure of this folder evolved a lot in a short time, this resulted into multiple test methods being
used: unit tests with phpunit, unit tests with phpunit using PrestaShop Core classes or a database fixtures,
unit tests using Symfony testcase ...

So they have been moved in this folder in order to clean `tests` directory where
a new test structure will be created.

### Here are the rules if you need to modify a file in this folder. Only allowed changes are:
- Minor update of a test after a behavior has been modified into PrestaShop project (example: new constructor argument)
- If it requires a major update, the test must be rewritten to fit the new test structure. Then the legacy test in this
folder is deleted and the refactored version is added in `tests` folder
