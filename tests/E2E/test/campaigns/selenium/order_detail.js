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
const {CheckoutOrderPage, CustomerAccount} = require('../../selectors/FO/order_page');
let promise = Promise.resolve();

scenario('Order history page', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
    test('should set the language of shop to "English"', () => client.changeLanguage());
  }, 'common_client');
  scenario('Display a list of orders', client => {
    test('should go to the customer account', () => client.waitForExistAndClick(CheckoutOrderPage.customer_name));
    test('should display a list of orders', () => {
      return promise
        .then(() => client.waitForExistAndClick(CustomerAccount.order_history_button))
        .then(() => client.checkList(CustomerAccount.details_buttons))
    });
    test('should click on the "Details" button', () => client.waitForExistAndClick(CustomerAccount.details_button.replace("%NUMBER", 5)));
  }, 'common_client');
  scenario('Order detail page', client => {
    test('should check that is the order details page', () => client.checkTextValue(CustomerAccount.order_details_words, "Order details"));
    test('should display order infos', () => client.waitForVisible(CustomerAccount.order_infos_block));
    test('should display order statuses', () => client.waitForVisible(CustomerAccount.order_status_block));
    test('should display invoice address', () => client.waitForVisible(CustomerAccount.invoice_address_block));
    test('should display order products', () => client.waitForVisible(CustomerAccount.order_products_block));
    test('should display the return button', () => client.waitForVisible(CustomerAccount.order_products_block));
    test('should display a form to add a message', () => client.waitForVisible(CustomerAccount.add_message_block));
    test('should add a message', () => client.waitAndSetValue(CustomerAccount.message_input, "Message about the first order product"));
    test('should click on the "SEND" button', () => client.waitForExistAndClick(CustomerAccount.send_button));
    test('should verify the appearance of the green validation', () => {
      return promise
        .then(() => client.waitForVisible(CustomerAccount.success_panel))
        .then(() => client.checkTextValue(CustomerAccount.success_panel, 'Message successfully sent'))
    });
  }, 'common_client');
}, 'common_client',true);
