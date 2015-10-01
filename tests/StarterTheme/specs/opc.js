/* global describe, it, browser, before */

var fixtures = require('../fixtures');
var q        = require('q');

describe('The One Page Checkout', function () {
  describe('without the Advanced payment API', function () {
    before(function () {
      return browser.url(fixtures.urls.opc + '?debug-set-configuration-PS_ADVANCED_PAYMENT_API=0');
    });

    it('should display terms and conditions', function () {
      return browser.element('#conditions-to-approve');
    });

    it('should not display payment modules until the checkboxes are checked', function () {
      return browser.elements('.payment_module').then(function (elements) {
        elements.value.length.should.equal(0);
      });
    });

    it('should display payment modules when all checkboxes are checked', function () {

      // We cannot loop on the checkboxes to check them because the DOM
      // updates everytime a checkbox is checked
      // so instead we check them one by one and wait a bit before
      // going to the next one.
      function checkRemainingCheckboxes () {
        return browser.elements('#conditions-to-approve input[type="checkbox"]:not(:checked)')
          .then(function (elements) {
            if (elements.value.length > 0) {
              return browser
                .elementIdClick(elements.value[0].ELEMENT)
                .then(function () {
                  return browser.pause(500);
                })
                .then(checkRemainingCheckboxes)
              ;
            }
          });
      }

      return checkRemainingCheckboxes().then(function () {
        return browser.waitForVisible('.payment_module');
      });
    });
  });

  describe('with the Advanced payment API', function () {
    before(function () {
      return browser.url(fixtures.urls.opc + '?debug-set-configuration-PS_ADVANCED_PAYMENT_API=1');
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
      return browser.elements('#conditions-to-approve label').then(function (elements) {
        return q.all(elements.value.map(function (element) {
          return browser.elementIdClick(element.ELEMENT);
        }))
        .then(function () {
          return browser.click('.advanced-payment-option:first-of-type label');
        })
        .then(function () {
          return browser.waitForEnabled('#payment-confirmation button');
        });
      });
    });
  });
});
