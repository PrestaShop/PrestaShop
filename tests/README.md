Tests
=====

This is PrestaShop automated tests folder.

Multiple type of tests are available:
- `UI` folder contains user interface tests, see below
- `Unit` folder contains unit tests, see below
- `Integration` folder contains integration tests, see below

- `Resources` contains different resources need for some tests (code snippets, dummy modules, dummy themes,  example files)
- `TestCase` contains some helper classes

This folder also contains a few scripts. They are used in our CI environment to trigger our different test campaigns.

# e2e tests

PrestaShop end-to-end tests are powered by a tool suite as it requires:
- to control a browser behavior
- to mimic a user's behavior
- to setup a test case and validate the behavior

This folder contains our [pages objects](https://martinfowler.com/bliki/PageObject.html) and our test code, 
organized in campaigns.

We use [Mocha](https://mochajs.org/), [Playwright](https://github.com/microsoft/playwright) and 
[Chai](https://www.chaijs.com/) as our base stack.

## How to write e2e tests

Please refer to our [documentation](https://devdocs.prestashop.com/1.7/testing/how-to-create-your-own-web-acceptance-tests/).

# Unit tests

PrestaShop unit tests are powered by [phpunit][1].

## How to write unit tests

- One php class = one test file.
- The test filepath must follow the class filepath/
- Every dependency of the class must be replaced by [test doubles][2]*.

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

[1]: https://phpunit.de/
[2]: https://martinfowler.com/articles/mocksArentStubs.html#TheDifferenceBetweenMocksAndStubs
[3]: http://behat.org/en/latest/
