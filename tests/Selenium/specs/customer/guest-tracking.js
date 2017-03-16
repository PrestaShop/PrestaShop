/* global describe, it, browser, before, after */

import fixtures from '../../fixtures';

describe('Order tracking', function () {
  describe('the Guest order tracking page', function () {

    it('should display the guest order tracking form', function () {
      return browser
        .url(fixtures.urls.guestTracking)
        .isVisible('#guestOrderTrackingForm').should.become(true)
      ;
    });

    it('should take you to the login form (order #5)', function () {
      return browser
        .url(fixtures.urls.guestTracking)
        .setValue('#guestOrderTrackingForm [name="order_reference"]', fixtures.order.reference)
        .setValue('#guestOrderTrackingForm [name="email"]', fixtures.customer.email)
        .submitForm('form#guestOrderTrackingForm')
        .followRedirect()
        .waitForVisible('form#login-form')
      ;
    });

  });
});
