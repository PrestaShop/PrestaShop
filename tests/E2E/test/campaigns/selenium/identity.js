const {AccessPageFO} = require('../../selectors/FO/access_page');
const {accountPage} = require('../../selectors/FO/add_account_page');
const customer = require('../common_scenarios/customer');
const order = require('../common_scenarios/order');
let promise = Promise.resolve();

scenario('Customer Identity', () => {
  scenario('Open the browser and access to the FO', client => {
    test('should open the browser', () => client.open());
    test('should access to FO', () => client.accessToFO(AccessPageFO));
    test('should change the FO language to english', () => client.changeLanguage());
  }, 'customer');
  scenario('The Customer Identity Page', client => {
    test('should show the customer form', () => {
      return promise
        .then(() => client.scrollWaitForExistAndClick(AccessPageFO.personal_info, 150, 2000))
        .then(() => client.waitAndSetValue(accountPage.signin_email_input, "pub@prestashop.com"))
        .then(() => client.waitAndSetValue(accountPage.signin_password_input, "123456789"))
        .then(() => client.waitForExistAndClick(AccessPageFO.login_button))
        .then(() => client.isVisible(accountPage.customer_form))
        .then(() => expect(global.isVisible).to.be.true);
    });
    test('should refuse to save the customer if the wrong password is provided', () => {
      return promise
        .then(() => client.waitAndSetValue(accountPage.password_account_input, "wrongPassword"))
        .then(() => client.waitForVisibleAndClick(accountPage.save_account_button))
        .then(() => client.waitForVisible(accountPage.danger_alert))
    });
    test('should save the customer if the correct password is provided', () => {
      return promise
        .then(() => client.waitAndSetValue(accountPage.password_account_input, "123456789"))
        .then(() => client.waitForExistAndClick(accountPage.save_account_button))
        .then(() => client.waitForVisible(accountPage.success_alert));
    });
    test('should allow the customer to change their password', () => {
      return promise
        .then(() => client.waitAndSetValue(accountPage.password_account_input, "123456789"))
        .then(() => client.waitAndSetValue(accountPage.new_password_input, "newPassword"))
        .then(() => client.waitForExistAndClick(accountPage.save_account_button))
        .then(() => client.waitForVisible(accountPage.success_alert));
    });
    test('should allow the customer to use the new password', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageFO.sign_out_button))
        .then(() => client.waitAndSetValue(accountPage.signin_email_input, "pub@prestashop.com"))
        .then(() => client.waitAndSetValue(accountPage.signin_password_input, "newPassword"))
        .then(() => client.waitForExistAndClick(AccessPageFO.login_button))
        .then(() => client.waitAndSetValue(accountPage.password_account_input, "newPassword"))
        .then(() => client.waitAndSetValue(accountPage.new_password_input, "123456789"))
        .then(() => client.waitForExistAndClick(accountPage.save_account_button))
        .then(() => client.waitForVisible(accountPage.success_alert));
    });
    test('should logout', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageFO.sign_out_button))
        .then(() => client.waitForExistAndClick(AccessPageFO.logo_home_page))
        .then(() => client.changeLanguage());
    });
  }, 'customer');

  scenario('The guest form during checkout', () => {
    scenario('Add product to cart as a guest', client => {
      order.initCheckout(client);
    }, 'order');

    scenario('the form can be filled a first time with an email address', client => {
      customer.fillGuestInfo('Fill the personal information step as a guest', client);
    }, 'customer');

    scenario('there can be 2 guests using the same e-mail address', () => {
      scenario('Delete cookies', client => {
        test('should delete cookies', () => client.deleteCookie());
        test('should click on "Continue shopping', () => client.waitForExistAndClick(accountPage.continue_shopping));
      }, 'customer');
      scenario('Add product to cart as a guest', client => {
        order.initCheckout(client);
      }, 'order');
      scenario('should let another guest use the same e-mail address', client => {
        customer.fillGuestInfo('should let another guest use the same e-mail address', client);
      }, 'customer');
    }, 'customer');

    scenario('Updating the guest account during checkout', client => {
      test('should let the guest update their lastname', () => {
        return promise
          .then(() => client.waitForExistAndClick(accountPage.checkout_step))
          .then(() => client.waitAndSetValue(accountPage.lastname_input, "a Ghost"))
          .then(() => client.waitForExistAndClick(accountPage.customer_form_continue_button))
          .then(() => client.waitForVisible(accountPage.checkout_step_complete));
      });
      test('should not let the guest change their email address to that of a real customer', () => {
        return promise
          .then(() => client.waitForExistAndClick(accountPage.checkout_step))
          .then(() => client.waitAndSetValue(accountPage.email_input, "pub@prestashop.com"))
          .then(() => client.waitForExistAndClick(accountPage.customer_form_continue_button))
          .then(() => client.isNotExisting(accountPage.checkout_step_complete));
      });
      test('should let the guest change their email address if not used by a customer', () => {
        return promise
          .then(() => client.waitAndSetValue(accountPage.email_input, "guest.guest@example.com"))
          .then(() => client.waitForExistAndClick(accountPage.customer_form_continue_button))
          .then(() => client.waitForVisible(accountPage.checkout_step_complete));
      });
      test('should let the guest add a password to create an account', () => {
        return promise
          .then(() => client.waitForExistAndClick(accountPage.checkout_step))
          .then(() => client.waitAndSetValue(accountPage.email_input, "test" + date_time + "@example.com"))
          .then(() => client.waitAndSetValue(accountPage.password_account_input, "123456789"))
          .then(() => client.waitForExistAndClick(accountPage.customer_form_continue_button))
          .then(() => client.waitForVisible(accountPage.checkout_step_complete))
          .then(() => client.waitForExistAndClick(accountPage.checkout_step))
          .then(() => client.isExisting(accountPage.checkout_step))

      });
    }, 'customer');
  }, 'customer');
}, 'customer', true);
