/* global describe, it, browser, before */

var fixtures = require('../../fixtures');
var q        = require('q');

describe('Customer account: My account page', function () {

  before(function () {
    return browser
            .url(fixtures.urls.login)
            .setValue('.login-form input[name=email]', fixtures.customer.email)
            .setValue('.login-form input[name=passwd]', fixtures.customer.password)
            .submitForm('.login-form form')
            .pause(500);
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
              });
    });

    it('should display identity link', function () {
      return browser
              .isExisting('.page-content a#identity-link')
              .then(function (isExisting) {
                isExisting.should.be.true;
              });
    });

    it('should display history link', function () {
      return browser
              .isExisting('.page-content a#history-link')
              .then(function (isExisting) {
                isExisting.should.be.true;
              });
    });

  });

  after(function () {
    return browser.url(fixtures.urls.logout).pause(500);
  })

});
