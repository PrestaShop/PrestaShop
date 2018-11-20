/* global describe, it, before, after, browser */

import fixtures from '../fixtures';
import * as checkout from '../helpers/checkout';
import {getRandomUser} from '../helpers/random-user';

const guestScenario = {
    name: "Guest Checkout",
    customerDoesntHaveAnAccount: true,
    customerOrdersAsGuest: true,
    deliveryAddressIsInvoiceAddress: true,
    customerHasAnAddress: false
};

const registrationScenario = Object.assign({}, guestScenario, {
  name: "Registration Checkout",
  customerOrdersAsGuest: false
});

const guestScenarioDifferentAddresses = Object.assign({}, guestScenario, {
  name: "Guest Checkout With Different Invoice Address",
  deliveryAddressIsInvoiceAddress: false
});

const existingCustomerScenario = Object.assign({}, guestScenario, {
  name: "Checkout by Existing Customer Not Logged-In Yet",
  customerDoesntHaveAnAccount: false,
  customerOrdersAsGuest: false,
  customerHasAnAddress: true,
  deliveryAddressIsInvoiceAddress: true
});

const scenarios = [
  existingCustomerScenario,
  guestScenario,
  guestScenarioDifferentAddresses,
  registrationScenario
];

describe("The Checkout Process", function () {
    scenarios.forEach(runScenario);
});

function runScenario (scenario) {
  describe(scenario.name, function () {

    let user;

    before(function () {
      return Promise.all([
        checkout.addSomeProductToCart(),
        getRandomUser().then(randomUser => user = randomUser)
      ]).then(() => browser.url(fixtures.urls.checkout));
    });

    after(function () {
        return browser.deleteCookie().url('/');
    });

    describe("The Steps Of The Order Process", function () {
        it('should show the personal information step as reachable', function () {
          return browser.waitForVisible(
            '#checkout-personal-information-step.-reachable'
          );
        });

        it('should show the account creation form', function () {
          return browser.waitForVisible('#customer-form');
        });

        it('should show a link to the login form', function () {
          return browser.waitForVisible(
            '#checkout-personal-information-step [data-link-action="show-login-form"]'
          );
        });

        describe('the personal information step', function () {
          if (scenario.customerOrdersAsGuest || scenario.customerDoesntHaveAnAccount) {
            let infoFormTestText = 'should allow filling the personal info form';
            if (scenario.customerOrdersAsGuest) {
              infoFormTestText += ' without password';
            }

            it(infoFormTestText, function () {
              return browser
                .setValue('#customer-form [name=firstname]', user.name.first)
                .setValue('#customer-form [name=lastname]' , user.name.last)
                .setValue('#customer-form [name=email]'    , user.email)
                .then(() => {
                  if (!scenario.customerOrdersAsGuest) {
                    return browser.setValue('#customer-form [name=password]', '123456789');
                  }
                })
                .click('#customer-form button[data-link-action="register-new-customer"]')
                .waitForVisible(
                    '#checkout-personal-information-step.-reachable.-complete'
                )
              ;
            });
          } else {
            it('should let the user login instead of registering', function () {
              return browser
                .click('[data-link-action="show-login-form"]')
                .waitForVisible('#login-form')
                .setValue('#login-form [name=email]', fixtures.customer.email)
                .setValue('#login-form [name=password]', fixtures.customer.password)
                .click('#login-form button[data-link-action="sign-in"]')
              ;
            });
          }
        });

        describe('the addresses step', function () {
          it('should show the addresses step as reachable', function () {
            return browser.waitForVisible(
              '#checkout-addresses-step.-reachable'
            );
          });

          if (!scenario.customerHasAnAddress) {
            it("should not show any addresses", function () {
              return browser.isVisible('.address-item').should.become(false);
            });

            it("should show the delivery address form", function () {
              return browser.waitForVisible('form #delivery-address');
            });

            it("the delivery address form should have the customer firstname and lastname pre-filled", function () {
              return browser
                .getValue('#delivery-address [name=firstname]')
                .should.become(user.name.first)
                .then(() => browser.getValue('#delivery-address [name=lastname]'))
                .should.become(user.name.last)
              ;
            });

            it("should fill the address form and go to delivery step", function () {
              return browser
                .setValue('#delivery-address [name=address1]', user.location.street)
                .setValue('#delivery-address [name=city]', user.location.city)
                .setValue('#delivery-address [name=postcode]', '00000')
                .setValue('#delivery-address [name=phone]', '0123456789')
                .click('#delivery-address button')
                .waitForVisible('#checkout-delivery-step.-reachable')
              ;
            });
          } else {
            it("should have an existing address pre-selected", function () {
              return browser
                .waitForVisible('#checkout-addresses-step.-current', 5000)
                .isSelected('[name="id_address_delivery"]')
                .should.become.true
              ;
            });
            it("should go to the next step once the customer clicks continue", function () {
              return browser.click('#checkout-addresses-step button.continue');
            });
          }

          if (!scenario.deliveryAddressIsInvoiceAddress) {
            it("should open another address form for the invoice address", function () {
              return browser
                .click('#checkout-addresses-step')
                .click('[data-link-action="different-invoice-address"]')
                .waitForVisible('form #invoice-address')
              ;
            });

            it("should still show the delivery address", function () {
              return browser.waitForVisible('#delivery-addresses .address-item');
            });

            it("but without edit button", function () {
              return browser.isVisible('#delivery-addresses .address-item [data-link-action="edit-address"]').should.become(false);
            });

            it("should fill the invoice address form", function () {
              return browser
                .setValue('#invoice-address [name=firstname]', 'Someone')
                .setValue('#invoice-address [name=lastname]', 'Else')
                .setValue('#invoice-address [name=address1]', user.location.street)
                .setValue('#invoice-address [name=city]', user.location.city)
                .setValue('#invoice-address [name=postcode]', '11111')
                .setValue('#invoice-address [name=phone]', '0123456789')
                .click('#invoice-address button')
              ;
            });

            it('should have gone to the next step after clicking on the button in the invoice address form', function () {
              return browser.waitForVisible('#checkout-delivery-step.-current');
            });
          }

          it('should show the addresses step as "done"', function () {
            return browser.waitForVisible(
              '#checkout-addresses-step.-complete'
            );
          });
        });

        describe('the delivery step', function () {
          it("should show delivery options", function () {
            return browser
              .waitForVisible('.delivery-options-list')
            ;
          });
          it("the delivery options have an impact on cart summary display", function () {
            var cartSummary = browser.getValue('#js-cart-summary');
            return browser
              .click('#delivery_option_2')
              .getValue('#js-cart-summary')
              .should.not.equal(cartSummary)
            ;
          });
          it("the gift display check would have an impact on cart summary display", function () {
            var cartSummary = browser.getValue('#js-cart-summary');
            return browser
              .click('input.js-gift-checkbox')
              .getValue('#js-cart-summary')
              .should.not.equal(cartSummary)
              ;
          });
          it('should be marked as complete after user has clicked continue', function () {
            return browser
              .pause(5000)
              .click('#checkout-delivery-step button')
              .waitForVisible('#checkout-delivery-step.-complete')
              .catch(err =>
                browser
                  .getSource()
                  .then(
                    source => console.log(source)
                  )
                  .then(function () {
                    throw err;
                  })
              );
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
            return browser.waitForVisible('.payment-options .payment-option');
          });

          it("should have a disabled order button", function () {
            return browser.isEnabled('#payment-confirmation button')
              .should.become(false)
            ;
          });

          it("should check the terms-and-conditions", function () {
            return browser.click(
              '[name="conditions_to_approve[terms-and-conditions]"]'
            );
          });
          it("should choose a payment option", function () {
            return browser.click(
              '.payment-options .payment-option label'
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
