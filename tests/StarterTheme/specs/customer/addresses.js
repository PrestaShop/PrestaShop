/* global describe, it, browser, before */

var fixtures = require('../../fixtures');
var q        = require('q');
var _        = require('underscore');

describe.only('Customer account: Addresses', function () {

  before(function () {
    return browser
            .url(fixtures.urls.login)
            .setValue('.login-form input[name=email]', fixtures.customer.email)
            .setValue('.login-form input[name=passwd]', fixtures.customer.password)
            .submitForm('.login-form form')
            .pause(500);
  });

  describe('The customer can access his account', function () {

    before(function () {
      return browser.url(fixtures.urls.myAccount);
    });

  });

  describe('The customer already have addresses', function () {
    var initialAddressesCount = 0;

    it('should display a list of addresses', function () {
      return browser
              .url(fixtures.urls.myAddresses)
              .elements('.page-addresses article.address')
              .then(function (elements) {
                initialAddressesCount = elements.value.length;
                initialAddressesCount.should.be.greaterThan(0);
              });
    });

    it('should allow customer to edit it', function () {
      var addressAlias = 'Edit address '+_.now();

      return browser
              .element('a[data-link-action="edit-address"]')
              .click()
              .pause(500)
              .setValue('.address-form input[name=alias]', addressAlias)
              .submitForm('.address-form form')
              .element('a[data-link-action="edit-address"]')
              .click()
              .pause(500)
              .getValue('.address-form input[name=alias]')
              .then(function (value) {
                value.should.equals(addressAlias);
              })
    });

    var idAddressCreated = 0;
    it('should allow customer to create a new address', function () {
      var initialAddressesCount = 0;
      return browser
              .url(fixtures.urls.myAddresses)
              .elements('article.address')
              .then(function (elements) {
                initialAddressesCount = elements.value.length;
              })
              .element('a[data-link-action="add-address"]')
              .click()
              .pause(500)
              .setValue('.address-form input[name=address1]', '12 rue d\'Amsterdam')
              .setValue('.address-form input[name=city]', 'Paris City')
              .setValue('.address-form input[name=postcode]', '75009')
              .setValue('.address-form input[name=phone]', '1234567890')
              .setValue('.address-form input[name=alias]', 'Selenium address '+_.now())
              .submitForm('.address-form form')
              .elements('article.address')
              .then(function (elements) {
                var lastElement = _.last(elements.value);
                browser.elementIdAttribute(lastElement.ELEMENT, 'data-id-address').then(function (id) {
                  idAddressCreated = id.value;
                });
              })
              .elements('article.address')
              .then(function (elements) {
                var finalAddressesCount = initialAddressesCount+1;
                elements.value.length.should.equals(finalAddressesCount);
              })
    });

    it('should allow customer to delete an address', function () {
      return browser
              .click('#address-'+idAddressCreated+' a[data-link-action="delete-address"]')
              .pause(500)
              .isExisting('article#address-'+idAddressCreated)
              .then(function (isExisting) {
                isExisting.should.be.false;
              });
    })

  });

  after(function () {
    return browser.url(fixtures.urls.logout).pause(500);
  });

});
