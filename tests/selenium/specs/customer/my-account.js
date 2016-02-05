/* global describe, it, browser, before, after */

var fixtures = require('../../fixtures');

describe('My account page', function () {

  before(function () {
    return browser.loginDefaultCustomer();
  });

  after(function () {
    return browser.logout();
  });

  describe('The customer account has basic feature', function () {

    before(function () {
      return browser.url(fixtures.urls.myAccount);
    });

    it('should display addresses link', function () {
      return browser
        .elements('.page-content a#addresses-link, .page-content a#address-link')
        .then(function (elements) {
          elements.value.length.should.be.greaterThan(0);
        })
      ;
    });

    it('should display identity link', function () {
      return browser
        .isExisting('.page-content a#identity-link')
        .then(function (isExisting) {
          isExisting.should.be.true;
        })
      ;
    });

    it('should display history link', function () {
      return browser
        .isExisting('.page-content a#history-link')
        .then(function (isExisting) {
          isExisting.should.be.true;
        })
      ;
    });

    it('should display credit splips link', function () {
      return browser
        .isExisting('.page-content a#order-slips-link')
        .then(function (isExisting) {
          isExisting.should.be.true;
        })
      ;
    });

    it('should display vouchers link', function () {
      return browser
        .isExisting('.page-content a#discounts-link')
        .then(function (isExisting) {
          isExisting.should.be.true;
        })
      ;
    });

    it('should display returns link', function () {
      return browser
        .isExisting('.page-content a#returns-link')
        .then(function (isExisting) {
          isExisting.should.be.true;
        })
      ;
    });

  });
});
