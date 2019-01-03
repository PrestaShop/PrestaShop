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
const commonScenarios = require('../../common_scenarios/discount');
const welcomeScenarios = require('../../common_scenarios/welcome');
let cartRuleData = [
  {
    name: 'Percent',
    customer_email: 'pub@prestashop.com',
    minimum_amount: 20,
    type: 'percent',
    reduction: 50
  },
  {
    name: 'Amount',
    customer_email: 'pub@prestashop.com',
    minimum_amount: 20,
    type: 'amount',
    reduction: 20
  }
];

scenario('Create, edit, check and delete "Cart Rule" in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'discount');
  welcomeScenarios.findAndCloseWelcomeModal();
  for (let i = 0; i < cartRuleData.length; i++) {
    commonScenarios.createCartRule(cartRuleData[i], 'code' + (i + 1));
    commonScenarios.checkCartRule(cartRuleData[i], 'code' + (i + 1));
    commonScenarios.editCartRule(cartRuleData[i]);
    commonScenarios.checkCartRule(cartRuleData[i], 'code' + (i + 1));
    commonScenarios.deleteCartRule(cartRuleData[i].name);
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'discount');
}, 'discount', true);
