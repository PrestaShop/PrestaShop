/* global describe, it, browser, before, after */

import fixtures from '../../fixtures';

describe('Order history page', function () {

  before(function () {
    return browser.loginDefaultCustomer();
  });

  after(function () {
    return browser.logout();
  });

  it('should display a list of orders', function () {
    return browser
        .url(fixtures.urls.orderhistory)
        .elements('a[data-link-action="view-order-details"]')
        .then(function (elements) {
          elements.value.length.should.be.greaterThan(0);
        })
      ;
  });

  it('should allow customer to see details', function () {
    return browser
        .url(fixtures.urls.orderhistory)
        .click('(//a[@data-link-action="view-order-details"])[1]')
        .waitForVisible('.page-content #order-infos')
      ;
  });

  describe('Order detail page', function() {

    before(function () {
      return browser
          .url(fixtures.urls.orderdetail);
    });

    it('should display order infos', function () {
      return browser.waitForVisible('.page-content #order-infos');
    });

    it('should display order statuses', function () {
      return browser.waitForVisible('.page-content #order-history');
    });

    it('should display invoice address', function () {
      return browser.waitForVisible('.page-content #invoice-address');
    });

    it('should display order products', function () {
      return browser.waitForVisible('.page-content #order-products');
    });

    it('should display the return button', function () {
      return browser.waitForVisible('.page-content button[name=submitReturnMerchandise]');
    });

    it('should display a form to add a message', function () {
      return browser.waitForVisible('.page-content .order-message-form');
    });

    it('should allow customer to add a message', function () {
      return browser
          .selectByIndex('.order-message-form select[name=id_product]', 0)
          .setValue('.order-message-form textarea[name=msgText]', 'Message about the first order product')
          .click('.page-content button[name=submitMessage]')
          .waitForVisible('article[role="alert"][data-alert="success"]')
        ;
    });

  });

});
