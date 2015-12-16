/* global describe, it, before, after, browser */

import fixtures from '../fixtures';
import * as checkout from '../helpers/checkout';
import {getRandomUser} from '../helpers/random-user';

const guestScenario = {
    name: "Guest Checkout",
    customerDoesntHaveAnAccount: true,
    customerOrdersAsGuest: true,
    deliveryAddressIsInvoiceAddress: true
};

const registrationScenario = Object.assign({}, guestScenario, {
  name: "Registration Checkout",
  customerOrdersAsGuest: false
});

const guestScenarioDifferentAddresses = Object.assign({}, guestScenario, {
  name: "Guest Checkout With Different Invoice Address",
  deliveryAddressIsInvoiceAddress: false
});

const scenarios = [
  guestScenarioDifferentAddresses,
  guestScenario,
  registrationScenario
];

describe.only("The Checkout Process", function () {
    scenarios.forEach(runScenario);
});

function runScenario (scenario) {
  describe(scenario.name, function () {

    before(function () {
        return checkout.addSomeProductToCart().then(
            () => browser.url(fixtures.urls.checkout)
        );
    });

    after(function () {
        return browser.deleteCookie().url('/');
    });

    let user;
    if (scenario.customerDoesntHaveAnAccount) {
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

        describe('the personal information step', function () {
          it('should fail to submit the registration form if there are missing fields', function () {
            return browser
              .click('.customer-info-form button')
              .waitForVisible('.notification-error')
            ;
          });

          let infoFormTestText = 'should allow filling the personal info form';
          if (scenario.customerOrdersAsGuest) {
            infoFormTestText += ' without password';
          }

          it(infoFormTestText, function () {
            return browser
              .setValue('.customer-info-form [name=firstname]', user.name.first)
              .setValue('.customer-info-form [name=lastname]' , user.name.last)
              .setValue('.customer-info-form [name=email]'    , user.email)
              .then(() => {
                if (!scenario.customerOrdersAsGuest) {
                  return browser.setValue('.customer-info-form [name=passwd]', '123456789');
                }
              })
              .click('.customer-info-form button')
              .waitForVisible(
                  'section#personal-information-section[data-checkout-step-status="done"]'
              )
            ;
          });
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

          it("should fill the address form", function () {
            return browser
              .setValue('#checkout-address-delivery [name=address1]', user.location.street)
              .setValue('#checkout-address-delivery [name=city]', user.location.city)
              .setValue('#checkout-address-delivery [name=postcode]', '00000')
              .setValue('#checkout-address-delivery [name=phone]', '0123456789')
              .click('#checkout-address-delivery button')
              .waitForVisible('#delivery-addresses .address-item')
            ;
          });

          if (!scenario.deliveryAddressIsInvoiceAddress) {
            it("should open and fill another address form for the invoice address", function () {
              return browser
                .click('[data-link-action="setup-invoice-address"]')
                .setValue('#checkout-address-invoice [name=firstname]', 'Someone')
                .setValue('#checkout-address-invoice [name=lastname]', 'Else')
                .setValue('#checkout-address-invoice [name=address1]', user.location.street)
                .setValue('#checkout-address-invoice [name=city]', user.location.city)
                .setValue('#checkout-address-invoice [name=postcode]', '11111')
                .setValue('#checkout-address-invoice [name=phone]', '0123456789')
                .click('#checkout-address-invoice button')
                .waitForVisible('#invoice-addresses .address-item')
              ;
            });
          }

          it('should then show the addresses step as "done"', function () {
            return browser.waitForVisible(
              'section#addresses-section[data-checkout-step-status="done"]'
            );
          });
        });

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

      if (!scenario.customerOrdersAsGuest) {
        describe("after the order, when the customer did create an account", function () {
          it('the customer should be logged in', function () {
            return browser
              .waitForVisible('a.logout')
              .waitForVisible('a.account')
            ;
          });
        });
      }

  });
}
