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
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const commonOrder = require('../../common_scenarios/order');
let promise = Promise.resolve();
scenario('Generate and check a Credit slip', () => {
  scenario('Open the browser login successfully in the Back Office ', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  commonOrder.enableMerchandise();
  scenario('Create order and generate a credit slip', () => {
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
      test('should get the client Name & last name', () => client.getTextInVar(AccessPageFO.account, 'accountName'));
      test('should logout successfully from the Front Office', () => {
        return promise
          .then(() => client.signOutFO(AccessPageFO))
          .then(() => client.switchTab(0))
          .then(() => client.closeWindow(1));
      });
    }, 'common_client');
    scenario('Login in the Back Office ', client => {
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'common_client');
    commonOrder.creditSlip('2');
    commonOrder.checkCreditSlip('2');
  }, 'order');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'order', true);