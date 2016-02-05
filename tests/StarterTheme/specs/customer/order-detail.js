/* global describe, it, browser, before, after */

var fixtures = require('../../fixtures');

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
        .elements('.page-content a.order-detail-link')
        .then(function (elements) {
          initialOrdersCount = elements.value.length;
          initialOrdersCount.should.be.greaterThan(0);
        })
      ;
  });

  it('should allow customer to see details', function () {
    return browser
        .url(fixtures.urls.orderhistory)
        .click('.page-content a.order-detail-link')
        .isExisting('.page-content #order-infos')
        .then(function (isExisting) {
          isExisting.should.be.true;
        })
      ;
  });

  describe('Order detail page', function() {

    before(function () {
      return browser
          .url(fixtures.urls.orderdetail);
    });

    it('should display order infos', function () {
      return browser
          .isExisting('.page-content #order-infos')
          .then(function (isExisting) {
            isExisting.should.be.true;
          })
        ;
    });

    it('should display order statuses', function () {
      return browser
          .isExisting('.page-content #order-history')
          .then(function (isExisting) {
            isExisting.should.be.true;
          })
        ;
    });

    it('should display invoice address', function () {
      return browser
          .isExisting('.page-content #invoice-address')
          .then(function (isExisting) {
            isExisting.should.be.true;
          })
        ;
    });

    it('should display order products', function () {
      return browser
          .isExisting('.page-content #order-products')
          .then(function (isExisting) {
            isExisting.should.be.true;
          })
        ;
    });

    it('should display the return button', function () {
      return browser
          .isExisting('.page-content button[name=submitReturnMerchandise]')
          .then(function (isExisting) {
            isExisting.should.be.true;
          })
        ;
    });

    it('should display a form to add a message', function () {
      return browser
          .isExisting('.page-content .order-message-form')
          .then(function (isExisting) {
            isExisting.should.be.true;
          })
        ;
    });

    it('should allow customer to add a message', function () {
      return browser
          .selectByIndex('.order-message-form select[name=id_product]', 0)
          .setValue('.order-message-form textarea[name=msgText]', 'Message about the first order product')
          .click('.page-content button[name=submitMessage]')
          .isExisting('.notification-success')
          .then(function (isExisting) {
            isExisting.should.be.true;
          })
        ;
    });

  });

});
