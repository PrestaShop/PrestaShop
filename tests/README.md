Tests
=====

This is PrestaShop automated tests folder.

Multiple type of tests are available:
- `E2E` and `Selenium` folders contain end-to-end tests maintained by PrestaShop QA team
- `Unit` folder contain unit tests, see below

# Unit tests

PrestaShop unit tests are powered by phpunit.

## How to write unit tests

- one php class = one test file
- the test filepath must follow the class filepath
- every dependency of the class must be mocked*

*If there is a hard-coded dependency such as a singleton pattern being used
or a static call, this class cannot be unit tested and should be tested using
integration tests.

## Conventions

Use camelCase names for test function names.

# Integration tests

Incoming...
