/* global describe, it, browser, before, after */

var fixtures = require('../../fixtures');
var _ = require('underscore');

describe('Customer account: Addresses', function () {

  before(function () {
    return browser.loginDefaultCustomer();
  });

  after(function () {
    return browser.logout();
  });

  it('the customer should be able to access their account', function () {
    return browser.url(fixtures.urls.myAccount).element('body#my-account');
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
        .setValue('.address-form input[name=firstname]', 'Yolo')
        .setValue('.address-form input[name=lastname]', 'Really')
        .setValue('.address-form input[name=address1]', '12 rue d\'Amsterdam')
        .setValue('.address-form input[name=city]', 'Paris City')
        .then(() => browser.isVisible('.address-form select[name=id_state]').then(
          visible => {
            if (visible) {
              return browser.selectByIndex('.address-form select[name=id_state]', 0);
            }
          }
        ))
        .setValue('.address-form input[name=postcode]', '75009')
        .setValue('.address-form input[name=phone]', '1234567890')
        .setValue('.address-form input[name=alias]', 'Selenium address '+_.now())
        .submitForm('.address-form form')
        .elements('article.address')
        .then(function (elements) {
          var lastElement = _.last(elements.value);
          return browser.elementIdAttribute(lastElement.ELEMENT, 'data-id-address').then(function (id) {
            idAddressCreated = id.value;
          });
        })
        .elements('article.address')
        .then(function (elements) {
          var finalAddressesCount = initialAddressesCount+1;
          elements.value.length.should.equals(finalAddressesCount);
        });
    });

    it('should allow customer to edit it', function () {
      const addressAlias = 'Edit address '+_.now();

      return browser
        .element('#address-'+idAddressCreated+' a[data-link-action="edit-address"]')
        .click()
        .setValue('.address-form input[name=alias]', addressAlias)
        .submitForm('.address-form form')
        .element('#address-'+idAddressCreated+' a[data-link-action="edit-address"]')
        .click()
        .getValue('.address-form input[name=alias]')
        .then(function (value) {
          value.should.equals(addressAlias);
        });
    });

    it('should allow customer to delete an address', function () {
      return browser
        .url(fixtures.urls.myAddresses)
        .click('#address-'+idAddressCreated+' a[data-link-action="delete-address"]')
        .isExisting('article#address-'+idAddressCreated)
        .then(function (isExisting) {
          isExisting.should.be.false;
        });
    });

  });
});
