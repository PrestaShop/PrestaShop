/* global describe, it, browser, before, after */

var fixtures = require('../fixtures');
var q        = require('q');
var _        = require('underscore');

function toggleAllTermsCheckboxes () {
  return browser.elements('#conditions-to-approve label').then(function (elements) {
    return q.all(elements.value.map(function (element) {
      return browser.elementIdClick(element.ELEMENT);
    }));
  });
}

describe('The One Page Checkout', function () {
  describe('The customer is already logged in', function () {
    before(function () {
      return browser.url('/').click('.menu a').click('a[data-link-action="add-to-cart"]').pause(500) && browser.loginDefaultCustomer();
    });

    describe('Addresses management', function () {
      var initialAddressesCount = 0;

      it('should display customer addresses', function () {
        return browser
          .url(fixtures.urls.checkout)
          .elements('.address-selector .address-item')
          .then(function (elements) {
            initialAddressesCount = elements.value.length;
            initialAddressesCount.should.be.greaterThan(0);
          })
        ;
      });

      var newlyCreatedAddressId;
      it('should allow customer to create a new address', function () {
        return browser
          .click('a[data-link-action="add-new-address"]')
          .pause(500)
          .setValue('.address-form input[name=address1]', '12 rue d\'Amsterdam')
          .setValue('.address-form input[name=city]', 'Paris City')
          .setValue('.address-form input[name=postcode]', '75009')
          .setValue('.address-form input[name=phone]', '1234567890')
          .setValue('.address-form input[name=alias]', 'Selenium address '+_.now())
          .submitForm('.address-form form')
          .elements('.address-selector .address-item')
          .then(function (elements) {
            var newAddressesCount = elements.value.length;
            newAddressesCount.should.be.greaterThan(initialAddressesCount);
          })
          .getValue('#select-delivery-address .address-item:last-child [name="id_address_delivery"]')
          .then(function (id) {
            newlyCreatedAddressId = id;
          });
        ;
      });

      it('should save the new selected address', function () {
        return browser
          .click('#id-address-delivery-address-' + newlyCreatedAddressId + ' label')
          .submitForm('#checkout-addresses form')
          .url(fixtures.urls.checkout)
          .getValue('input[name="id_address_delivery"]:checked')
          .then(function (value) {
            value.should.equal(newlyCreatedAddressId);
          })
        ;
      });

    });


    function selectAddressesForOrder () {
      return browser
        .click('#select-delivery-address [name="id_address_delivery"]')
        .click('#select-invoice-address [name="id_address_invoice"]')
        .click('#checkout-addresses button[type="submit"]')
      ;
    }

    describe('Delivery options', function () {

      describe('with JS', function () {
        before(function () {
          return browser.url(fixtures.urls.checkout);
        });

        it('should display carriers', function () {
          return browser.element('#delivery-method');
        });

        it('should have one and only one carrier selected', function () {
          return browser.elements('#delivery-method input:checked').then(function (elements) {
            elements.value.length.should.equal(1);
          });
        });

        it('should remember carrier selected after reload', function () {
          return browser.click('#delivery-method input:not(:checked)').then(function () {
            browser.getAttribute('#delivery-method input:checked', 'id').then(function (firstattr) {
              browser.refresh().then(function () {
                browser.getAttribute('#delivery-method input:checked', 'id').then(function (secondattr) {
                  secondattr.should.equal(firstattr);
                });
              });
            });
          });
        });
      });

      describe('without JS', function () {
        before(function () {
          return browser.url(fixtures.urls.checkout + '?debug-disable-javascript=1');
        });

        it('should display carriers', function () {
          return browser.element('#delivery-method');
        });

        it('should have one and only one carrier selected', function () {
          return browser.elements('#delivery-method input:checked').then(function (elements) {
            elements.value.length.should.equal(1);
          });
        });

        it('should remember carrier selected after reload', function () {
          return browser.click('#delivery-method input:not(:checked)').then(function () {
            browser.getAttribute('#delivery-method input:checked', 'id').then(function (firstattr) {
              browser.click('#delivery-method button[type="submit"]').then(function () {
                browser.refresh().then(function () {
                  browser.getAttribute('#delivery-method input:checked', 'id').then(function (secondattr) {
                    secondattr.should.equal(firstattr);
                  });
                });
              });
            });
          });
        });
      });
    });

    describe('payment and conditions with JS', function () {
      before(function () {
        return browser
          .url(fixtures.urls.checkout)
        ;
      });

      it('should display terms and conditions', function () {
        return browser.element('#conditions-to-approve');
      });

      it('should display payment options', function () {
        return browser.elements('.advanced-payment-option').then(function (elements) {
          elements.value.length.should.be.greaterThan(0);
        });
      });

      it('should display a disabled order confirmation button until the checkboxes are checked', function () {
        return browser
          .isEnabled('#payment-confirmation button')
          .should.eventually.equal(false)
        ;
      });

      it('should enable the order confirmation button when all the checkboxes are checked', function () {
        return toggleAllTermsCheckboxes()
          .click('.advanced-payment-option:first-of-type label')
          .waitForEnabled('#payment-confirmation button')
        ;
      });
    });

    describe('payment and conditions without JS', function () {
      before(function () {
        return browser.url(fixtures.urls.checkout + '?debug-disable-javascript=1');
      });

      it('should not display payment module selection buttons upon reaching the page', function () {
        return browser.element('[name="select_payment_option"]').then(function () {
          throw new Error('Payment modules should not be selectable at this stage of the order.');
        }).catch(function () {
          // OK
        });
      });

      it('should display a button to approve all terms and conditions...', function () {
        return browser.element('#approve-terms');
      });


      it('...that should turn into a disapprove button once conditions are approved', function () {
        return toggleAllTermsCheckboxes()
          .click('#approve-terms')
          .waitForVisible('#disapprove-terms')
        ;
      });

      it('should allow selecting a payment method once terms are approved', function () {
        return browser.click('[name="select_payment_option"][value="advanced-payment-option-1"]');
      });

      it('should now allow paying with the selected option', function () {
        return browser.element('#payment-confirmation label[for="pay-with-advanced-payment-option-1"]');
      });
    });
  });
});
