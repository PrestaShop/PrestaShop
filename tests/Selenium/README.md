# PrestaShop StarterTheme tests

Selenium tests for the PrestaShop Starter Theme.

This test suite should **never be run on a production shop** because some tests are destructive.

## Setup

Dependencies:

- node.js
- npm
- java
- firefox

The usual:

- `cd tests/Selenium`
- `npm install`
- cross fingers

The less usual :

- install PrestaShop, preferably in English  with URL rewriting enabled (though tests should ideally be language and settings agnostic)
- copy `tests/Selenium/settings.dist.js` to `tests/Selenium/settings.js` and customize according to your setup
- once PrestaShop is installed, run `php prepare-shop.php`. **WARNING: never do this on a production shop because it will edit existing products without asking for your permission.**

## Usage

- `cd tests/Selenium`
- `npm run selenium`
- `npm test`
- cross fingers

## Developing Tests

### Summary

Tests are contained in the `specs` subfolder.

Until we can do more documentation, please have a look at the existing tests and at the [WebDriver.io API](http://webdriver.io/api.html).

If you need to add general purpose helper functions for your tests, they should go in `commands/init.js`.

If you need fixtures for your tests, please use the ones from the default installation or provide a script that installs them.

Do not hard-code things such as product ids in your tests: instead abstract them behind a name and put them in the `fixtures.js` file.

### More Details

#### Creating Tests

Our tests are run using the awesome [mocha](https://mochajs.org/) test runner, using the [chai](http://chaijs.com/) assertions framework with the `should` syntax. By default we're also configuring `chai` to use [chai-as-promised](https://github.com/domenic/chai-as-promised/) to make assertions on promises easier - more on this later.

To create new tests, just add a file inside the `specs` folder or add your tests to an existing spec file.
Related tests should live in the same spec file.

Tests are grouped inside `describe` calls and defined using the `it` function. This syntax encourages you to think about what you're doing from an end user point of view.

E.g. "`describe` the home page, `it` should contain a link with the logo" would be written in `home-page.js` as:

```javascript
describe('The home page', function () {
  it('should contain a link with the logo', function () {
    // your test here
  });
});
```

**So what should go into the body of the test?**

In all the tests, you have access to a global `browser` object, so let's use it:

```javascript
/* global describe, it, browser */
describe('The home page', function () {
  it('should contain a link with the logo', function () {
    return browser
      .url('/')
      .element('a.logo img')
    ;
  });
});
```

- Notice we're accessing the `/` URL, that's because the base URL is already defined in `settings.js` so that tests do not depend on your shop's URL. `webdriver.io` will automatically add the base URL.
- The test works because if the `a.logo img` element is not found then `webdriver.io` will throw an exception, which marks the test as failed for `mocha`.

#### Tips & Tricks

`webdriver.io` allows you to perform almost any action a browser would do using a fluent promise-based API.
You will need some familiarity with promises to make the most of the tool.

Here are a few examples to get you started.

##### Method Chaining

In `webdriver.io` all methods return promises, but if you don't care about the result of a call (e.g. if it is just an action) you can just chain methods and even though they are all asynchronous things will execute in order as you'd expect.

If some step fails, `webdriver.io` will throw an exception and your test will fail, which is what we want.

For instance to click on an item *and then* wait for an element to appear:

```javascript
browser
.click('#approve-terms')
.waitForVisible('#disapprove-terms')
```

*Have a look at the methods in the Utility section of the [webdriver doc](http://webdriver.io/api/utility/waitForExist.html), they're very helpful.*

If you care about the result of intermediate steps in your promise chain, then you need to make the chaining explicit:

```javascript
describe('The One Page Checkout', function () {
  it('should not display payment modules until the checkboxes are checked', function () {
    return browser
      .elements('.payment_module')
      .then(function (elements) {
        elements.value.length.should.equal(0);
      })
    ;
  });
});
```

Here we care about what `.elements` returns, so we use `then` to tap into the promise chain and inspect the `elements`.
If there are elements then the expression `elements.value.length.should.equal(0)` will throw an exception.
Since we're inside the promise's success handler, the exception will make the promise rejected.
And because we're returning this promise the test will be marked as failed.

##### Making Assertions on Promises

Without `chai-as-promised` if you wanted to make an assertion on a promise you would have to write something like:

```javascript
it('should display a disabled order confirmation button until the checkboxes are checked', function (done) {
  return browser
    .isEnabled('#payment-confirmation button')
    .then(function (enabled) {
      enabled.should.equal(false);
      done();
    })
    .catch(function (err) {
      done(err);
    });
  ;
});
```

But thanks to `chai-as-promised` you can make it much simpler:

```javascript
it('should display a disabled order confirmation button until the checkboxes are checked', function () {
  return browser
    .isEnabled('#payment-confirmation button')
    .should.eventually.equal(false)
  ;
});
```
