/* global describe, it, browser, before, after */

import fixtures from '../../fixtures';
import * as checkout from '../../helpers/checkout';

describe('Customer Identity', function () {
  describe('the Customer Identity Page', function () {
    before(() => {
      return browser
        .loginDefaultCustomer()
        .url(fixtures.urls.identity)
      ;
    });

    it('should show the customer form', function () {
      return browser.isVisible('#customer-form').should.become(true);
    });

    it('should refuse to save the customer if the wrong password is provided', function () {
      return browser
        .setValue('[name="password"]', 'wrong password')
        .click('#customer-form button')
        .waitForVisible('.notification-error')
      ;
    });

    it('should save the customer if the correct password is provided', function () {
      return browser
        .setValue('[name="password"]', '123456789')
        .click('#customer-form button')
        .waitForVisible('.notification-success')
      ;
    });

    it('should allow the customer to change their password', function () {
      return browser
        // change the password
        .setValue('[name="password"]', '123456789')
        .setValue('[name="new_password"]', 'new password')
        .click('#customer-form button')
        .waitForVisible('.notification-success')
        // try to login with the new password
        .logout()
        .loginDefaultCustomer({password: 'new password'})
        // change it back to initial password
        .url(fixtures.urls.identity)
        .setValue('[name="password"]', 'new password')
        .setValue('[name="new_password"]', '123456789')
        .click('#customer-form button')
        .waitForVisible('.notification-success')
      ;
    });

    after(() => {
      return browser.logout();
    });
  });

  describe('the guest form during checkout', function () {

    function initCheckout () {
      return checkout
        .addSomeProductToCart()
        .url(fixtures.urls.checkout)
      ;
    }

    function cleanUp () {
      return browser.deleteCookie().url('/');
    }

    before(initCheckout);
    after(cleanUp);

    function fillGuestInfo () {
      return browser
        .setValue("#customer-form [name=firstname]", "I am")
        .setValue("#customer-form [name=lastname]", "a Guest")
        .setValue("#customer-form [name=email]", "guest@example.com")
        .click("#checkout-personal-information-step button")
        .waitForVisible("#checkout-personal-information-step.-complete")
      ;
    }

    it('should fill the personal information step as a guest', fillGuestInfo);

    describe("there can be 2 guests using the same e-mail address", function () {
      before(cleanUp);
      before(initCheckout);
      it('should let another guest use the same e-mail address', fillGuestInfo);
    });


    it('should let the guest update their lastname', function () {
      return browser
        .click("#checkout-personal-information-step h1")
        .setValue("#customer-form [name=lastname]", "a Ghost")
        .click("#checkout-personal-information-step button")
        .waitForVisible("#checkout-personal-information-step.-complete")
      ;
    });
  });
});
