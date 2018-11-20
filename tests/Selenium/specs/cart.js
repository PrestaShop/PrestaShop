/* global describe, it, before, after, browser */

import fixtures from '../fixtures';
import * as checkout from '../helpers/checkout';

describe("The shopping cart", function () {
    runScenario();
});

function runScenario () {
  describe('Add product to cart as a guest', function () {

    let cartSelector = '.js-cart';
    let cartProductCountSelector = '.js-subtotal';
    let increaseProductQuantitySelector = '.js-increase-product-quantity';
    let decreaseProductQuantitySelector = '.js-decrease-product-quantity';
    let cartItemSelector = '.cart-item';
    let removeProductFromCartSelector = cartItemSelector + ' .remove-from-cart';

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
        return browser
          .getText(cartProductCountSelector).should.become('1 item');
      });

      describe('The quantity input spinner', function () {
        it('should increase the product quantity', function () {
          return browser
            .click(increaseProductQuantitySelector)
            .pause(2000)
            .getText(cartProductCountSelector).should.become('2 items');
        });

        it('should decrease the product quantity', function () {
          return browser
            .click(decreaseProductQuantitySelector)
            .pause(2000)
            .getText(cartProductCountSelector).should.become('1 item');
        });
      });

      describe('The remove from cart button', function () {
        it('should remove a product from the cart', function () {
          return browser
            .click(removeProductFromCartSelector)
            .isExisting(cartItemSelector).then(function (isExisting) {
              return isExisting === false;
            });
        });
      })
    });
  });
}
