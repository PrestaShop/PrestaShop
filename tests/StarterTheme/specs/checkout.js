/* global describe, it, before, after, browser */

import fixtures from '../fixtures';
import * as checkout from '../helpers/checkout';
import {getRandomUser} from '../helpers/random-user';

const defaultScenario = {
    name: "Guest Checkout",
    customerOrdersAsGuest: true,
    deliveryAddressIsInvoiceAddress: true
};

const scenarios = [defaultScenario];

describe.only("The Checkout Process", function () {
    before(function () {
        return checkout.addSomeProductToCart().then(
            () => browser.url(fixtures.urls.checkout)
        );
    });

    after(function () {
        return browser.deleteCookie().refresh();
    });

    scenarios.forEach(runScenario);
});

function runScenario (scenario) {
  describe(scenario.name, function () {

    let user;
    if (scenario.customerOrdersAsGuest) {
      before(function () {
        return getRandomUser().then(randomUser => user = randomUser);
      });
    }

    describe("by default, customer is expected to order as guest", function () {
        it('should show the personal information step as "pending"', function () {
          return browser.waitForVisible(
            'section#personal-information-section[data-checkout-step-status="pending"]'
          );
        });

        it('should show the account creation form', function () {
          return browser.waitForVisible('.customer-info-form');
        });

        it('should show a link to the login form', function () {
          return browser.waitForVisible(
            '.customer-info-form [data-link-action="show-login-form"]'
          );
        });

        if (scenario.customerOrdersAsGuest) {
          it('should allow filling the personal info form without password', function () {
            return browser
              .setValue('.customer-info-form [name=firstname]', user.name.first)
              .setValue('.customer-info-form [name=lastname]' , user.name.last)
              .setValue('.customer-info-form [name=email]'    , user.email)
              .click('.customer-info-form button')
              .waitForVisible(
                  'section#personal-information-section[data-checkout-step-status="done"]'
              )
            ;
          });

          describe('the addresses step', function () {
            it('should show the addresses step as "pending"', function () {
              return browser.waitForVisible(
                'section#addresses-section[data-checkout-step-status="pending"]'
              );
            });

            it("should not show any addresses", function () {
              return browser.isVisible('.address-item').should.become(false);
            });

            it("should show the delivery address form", function () {
              return browser.waitForVisible('#checkout-address-delivery');
            });

            it("the delivery address form should have the customer firstname and lastname pre-filled", function () {
              return browser
                .getValue('#checkout-address-delivery [name=firstname]')
                .should.become(user.name.first)
                .then(() => browser.getValue('#checkout-address-delivery [name=lastname]'))
                .should.become(user.name.last)
              ;
            });

            it("should show an unchecked checkbox allowing to setup a different address", function () {
              return browser
                .isSelected('#checkout-different-address-for-invoice')
                .should.become(false);
            });

            it("should fill the address form", function () {
              browser
                .setValue('#checkout-address-delivery [name=address1]', user.location.street)
                .setValue('#checkout-address-delivery [name=city]', user.location.city)
                .setValue('#checkout-address-delivery [name=postcode]', '00000')
                .setValue('#checkout-address-delivery [name=phone]', '0123456789')
                .click('#checkout-address-delivery button')
                .waitForVisible('#checkout-address-delivery .address-item')
              ;
            });

            it('should then show the addresses step as "done"', function () {
              return browser.waitForVisible(
                'section#addresses-section[data-checkout-step-status="done"]'
              );
            });
          });
        }

        describe('the delivery step', function () {
          it("should show delivery options", function () {
            return browser.waitForVisible('.delivery-options .delivery-option');
          });
        });

        describe('the payment step', function () {
          it("should show a checkbox to accept the terms and conditions", function () {
            return browser.waitForVisible("#conditions-to-approve");
          });

          it("the terms and conditions checkbox should be unchecked", function () {
            return browser
              .isSelected('[name="conditions_to_approve[terms-and-conditions]"]')
              .should.become(false)
            ;
          });

          it("should show payment options", function () {
            return browser.waitForVisible('.advanced-payment-options .advanced-payment-option');
          });

          it("should have a disabled order button", function () {
            return browser.isEnabled('#payment-confirmation button')
              .should.become(false)
            ;
          });

          it("should check the terms-and-conditions", function () {
            return browser.click(
              'label[for="conditions_to_approve[terms-and-conditions]"'
            );
          });
          it("should choose a payment option", function () {
            return browser.click(
              '.advanced-payment-options .advanced-payment-option label'
            );
          });
          it("should confirm the payment", function () {
            return browser
              .click("#payment-confirmation button")
              .waitForVisible(".page-order-confirmation")
            ;
          });
        });
      });
  });
}
