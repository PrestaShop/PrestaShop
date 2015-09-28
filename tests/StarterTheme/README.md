# PrestaShop StarterTheme tests

Selenium tests for the PrestaShop Starter Theme.

This test suite should **never be run on a production shop**.

## Setup

Dependencies:

- node.js
- npm
- java
- firefox

The usual:

- `cd tests/StarterTheme`
- `npm install`
- cross fingers

The less usual :

- install PrestaShop, preferably in English (though tests should be language agnostic)
- copy `tests/StarterTheme/settings.dist.js` to `tests/StarterTheme/settings.js` and customize according to your setup
- once PrestaShop is installed, run `php prepare-shop.php`. **WARNING: never do this on a production shop because it will edit existing products without asking for your permission.**

## Usage

- `cd tests/StarterTheme`
- `npm run selenium`
- `npm test`
- cross fingers

## Development

Tests are contained in the `specs` subfolder.

Until we can do more documentation, please have a look at the existing tests and at the [WebDriver.io API](http://webdriver.io/api.html).

If you need to add general purpose helper functions for your tests, they should go in `commands/init.js`.

If you need fixtures for your tests, please use the ones from the default installation or provide a script that installs them.

Do not hard-code things such as product ids in your tests: instead abstract them behind a name and put them in the `fixtures.js` file.
