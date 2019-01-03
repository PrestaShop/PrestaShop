/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
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
