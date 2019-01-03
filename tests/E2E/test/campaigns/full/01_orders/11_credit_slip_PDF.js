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
const {Menu} = require('../../../selectors/BO/menu.js');
const {CreditSlip} = require('../../../selectors/BO/order');
const commonOrder = require('../../common_scenarios/order');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {MerchandiseReturns} = require('../../../selectors/BO/Merchandise_returns');
const common = require('../../../common.webdriverio');
let promise = Promise.resolve();
scenario('Create three credits slips and generate them', () => {
  scenario('Open the browser login successfully in the Back Office ', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Enable Merchandise Returns', () => {
    commonOrder.enableMerchandise(MerchandiseReturns)
  }, 'order');
  scenario('Create order and generate a credit slip', () => {
    for (let i = 0; i <= 2; i++) {
      scenario('Login in the Front Office ', client => {
        test('should access to the Front Office', () => {
          return promise
            .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
            .then(() => client.switchWindow(1));
        });
        test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
      }, 'common_client');
      commonOrder.createOrderFO();
      scenario('Get the account name and logout from the Front Office', client => {
        test('should get the account name logout successfully from the Front Office', () => {
          return promise
            .then(() => client.getTextInVar(AccessPageFO.account, 'accountName'))
            .then(() => client.signOutFO(AccessPageFO))
            .then(() => client.switchTab(0))
            .then(() => client.closeWindow(1));
        });
      }, 'common_client');
      scenario('Login in the Back Office ', client => {
        test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
      }, 'common_client');
      commonOrder.creditSlip('2', i);
      commonOrder.checkCreditSlip('2', i);
    }
  }, 'order');
}, 'order');
scenario('Filter by the issue date the credit slips', client => {
  let Date = common.getCustomDate(0);
  test('should go to the "Credit slips" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.credit_slips_submenu));
  test('should set "Form date" input', () => client.waitAndSetValue(CreditSlip.date_form, Date));
  test('should set "To date" input', () => client.waitAndSetValue(CreditSlip.date_to, Date));
  test('should click on "Generate PDF" button', () => client.waitForExistAndClick(CreditSlip.generate_button));
  for (let i = 0; i <= 2; i++) {
    commonOrder.checkCreditSlip('2', i);
  }
}, 'order');
scenario('Filter by a date which there is no order with credit slip generated', client => {
  test('should go to the "Credit slips" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.credit_slips_submenu));
  test('should set "Form date" input', () => client.waitAndSetValue(CreditSlip.date_form, '1900-01-01'));
  test('should set "To date" input', () => client.waitAndSetValue(CreditSlip.date_to, '1900-01-10'));
  test('should click on "Generate PDF" button', () => client.waitForExistAndClick(CreditSlip.generate_button));
  test('should check the error message existence', () => client.checkTextValue(CreditSlip.alerte_message, 'No order slips were found for this period.', 'contain'));
}, 'order', true);