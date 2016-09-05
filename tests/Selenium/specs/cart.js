/* global describe, it, before, after, browser */

import fixtures from '../fixtures';
import * as checkout from '../helpers/checkout';

describe("The shopping cart", function () {
    runScenario();
});

function runScenario () {
  describe('Add product to cart as a guest', function () {

    let cartSelector = '.js-cart';
    let cartProductCountSelector = '.cart-products-count';
    let increaseProductQuantitySelector = '.bootstrap-touchspin-up';
    let decreaseProductQuantitySelector = '.bootstrap-touchspin-down';
    let removeProductFromCartSelector = '.remove-from-cart';
    let cartItemSelector = '.cart-item';

    before(function () {
      return Promise.all([
        checkout.addSomeProductToCart()
      ]).then(() => browser.url(fixtures.urls.cart));
    });

    after(function () {
      return browser.deleteCookie().url('/');
    });

    describe("The shopping cart UI", function () {
      it('should display the cart', function () {
        return browser.waitForVisible(cartSelector);
      });

      it('should display the product quantity', function () {
        return browser.getText(cartProductCountSelector).then(function (text) {
          return text === '(1)';
        });
      });

      describe('The quantity input spinner', function () {
        it('should increase the product quantity', function () {
          return browser
            .click(increaseProductQuantitySelector)
            .waitUntil(function async () {
              return browser.getText(cartProductCountSelector).then(function (text) {
                return text === '(2)';
              });
            }, 5000, 'The cart product quantity should have been increased.');
        });

        it('should decrease the product quantity', function () {
          return browser
            .click(decreaseProductQuantitySelector)
            .waitUntil(function async () {
              return browser.getText(cartProductCountSelector).then(function (text) {
                return text === '(1)';
              })
            }, 5000, 'The cart product quantity should have been decreased.');
        });
      });

      describe('The remove from cart button', function () {
        it('should remove a product from the cart', function () {
          return browser
            .click(removeProductFromCartSelector)
            .waitForVisible(cartItemSelector, 2000, true) // Wait for cart item to be invisible
            .waitUntil(function async () {
              return browser.getText(cartProductCountSelector).then(function (text) {
                return text === '(0)';
              });
            }, 5000, 'The cart product quantity should have been zeroed.');
        });
      })
    });
  });
}
