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
const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const commonScenarios = require('../../../common_scenarios/taxes');
const welcomeScenarios = require('../../../common_scenarios/welcome');
let taxData = {
  name: 'VAT',
  tax_value: '19'
};

scenario('Create, edit, delete and check "Tax rules" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  commonScenarios.createTaxRule(taxData.name, taxData.tax_value);
  commonScenarios.checkTaxRule(taxData.name);
  commonScenarios.editTaxRule(taxData.name, taxData.name + 'update');
  commonScenarios.checkTaxRule(taxData.name + 'update');
  commonScenarios.deleteTaxRule(taxData.name + 'update');
  commonScenarios.createTaxRule(taxData.name, taxData.tax_value);
  commonScenarios.deleteTaxRuleWithBulkAction(taxData.name);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
