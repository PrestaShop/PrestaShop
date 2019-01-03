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
const {Menu} = require('../../../selectors/BO/menu');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const module_common_scenarios = require('../../common_scenarios/module');
let promise = Promise.resolve();

scenario('Check notification module in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Configure "Bank Transfer" module', client => {
    test('should go to "Modules" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Alerts" tab', () => {
      return promise
        .then(() => client.pause(4000))
        .then(() => client.getTextInVar(ModulePage.notification_number, 'notification'))
        .then(() => client.pause(4000))
        .then(() => client.waitForExistAndClick(Menu.Improve.Modules.alerts_subTab))
    });
    test('should click on "Configure" button', () => client.waitForExistAndClick(ModulePage.configure_link.replace('%moduleTechName', 'ps_wirepayment'),2000));
    test('should set the "Account owner" input', () => client.waitAndSetValue(ModulePage.ModuleBankTransferPage.account_owner_input, 'Demo'));
    test('should set the "Account details" textarea', () => client.waitAndSetValue(ModulePage.ModuleBankTransferPage.account_details_textarea, 'Check notification module'));
    test('should set the "Bank address" textarea', () => client.waitAndSetValue(ModulePage.ModuleBankTransferPage.bank_address_textarea, 'Boulvard street n°9 - 70501'));
    test('should click on "Save" button', () => client.waitForExistAndClick(ModulePage.ModuleBankTransferPage.save_button));
  }, 'common_client');
  scenario('Check that the module is well configured', client => {
    test('should go to "Modules" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu));
    test('should click on "Alerts" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.alerts_subTab));
    test('should check that the "Alerts number" is decremented with 1', () => client.checkTextValue(ModulePage.notification_number, (tab['notification'] - 1 ).toString(),'equal',1000));
    test('should check that the configured module is not visible in the "Alerts" tab', () => client.checkIsNotVisible(ModulePage.configure_module.replace('%moduleTechName', 'ps_wirepayment')));
  }, 'common_client');
  scenario('Reset the configured module', client => {
    module_common_scenarios.resetModule(client, ModulePage, AddProductPage, 'Bank transfer', 'ps_wirepayment');
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
