Tests
=====

This is PrestaShop automated tests folder.

Multiple type of tests are available:
- `E2E`  folder contains end-to-end tests
- `Unit` folder contains unit tests, see below
- `Integration` folder contains integration tests, see below

# Unit tests

PrestaShop unit tests are powered by [phpunit][2].

## How to write unit tests

- One php class = one test file.
- The test filepath must follow the class filepath/
- Every dependency of the class must be replaced by [test doubles][1]*.

*If there is a hard-coded dependency such as a singleton pattern being used
or a static call, this class cannot be unit tested and should be tested using
integration tests.

## Conventions

- Use camelCase names for test function names.
- Try to make method names explain the *intent* of the test case as best as possible. Don't hesitate to write long method names if necessary.
	- Bad example: `testGetPrice` (no idea what such a test is supposed to do)
	- Good example: `testDiscountIsAppliedToFinalPrice`

# Integration tests

PrestaShop integration tests are powered by [behat][3].

# End-to-end (e2e) tests

PrestaShop end-to-end tests are powered by a tool suite as it requires:
- to control a browser behavior
- to mimic a user's behavior
- to setup a test case and validate the behavior

[1]: https://martinfowler.com/articles/mocksArentStubs.html#TheDifferenceBetweenMocksAndStubs
[2]: https://phpunit.de/
[3]: http://behat.org/en/latest/
