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
const {AccessPageFO} = require('../../../../selectors/FO/access_page');
const commonScenarios = require('../../../common_scenarios/contact');

let contactData = {
  title: 'Service',
  email: 'prestotests@gmail.com',
  description: 'To contact the administration service'
}, messageData = {
  email: 'john.doe@prestashop.com',
  attachment: 'prestashop.png',
  message: 'Test send message'
};

scenario('Create, edit, delete and check "Contact" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Test 1: Create, check a "Contact" in the Back Office and check it in the Front Office', () => {
    commonScenarios.createContact(contactData);
    commonScenarios.checkContactBO(contactData);
    commonScenarios.configureContactFormModule();
    scenario('Go to the Front Office', client => {
      test('should go to the Front Office', () => client.accessToFO(AccessPageFO));
    }, 'common_client');
    commonScenarios.sendMessageFO(messageData);
    scenario('Go back to the Back Office', client => {
      test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
    }, 'common_client');
    commonScenarios.checkCustomerService(contactData, messageData);
  }, 'common_client');
  scenario('Test 2: Edit, check a "Contact" in the Back Office and check it in the Front Office', client => {
    test('should check and click on "Stop the OnBoarding" button', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.stop_button))
        .then(() => client.stopOnBoarding(OnBoarding.stop_button));
    });
    commonScenarios.editContact(contactData);
    commonScenarios.checkContactBO(contactData);
    scenario('Go to the Front Office', client => {
      test('should go to the Front Office', () => client.accessToFO(AccessPageFO));
    }, 'common_client');
    commonScenarios.checkContactFO(contactData);
    scenario('Go back to the Back Office', client => {
      test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
    }, 'common_client');
    commonScenarios.checkTitleCustomerService(contactData, messageData);
    commonScenarios.checkCustomerService(contactData, messageData, false, true);
  }, 'common_client');
  scenario('Test 3: Delete a "Contact" in the Back Office and check it in the Front Office', () => {
    commonScenarios.deleteContact(contactData);
    scenario('Go to the Front Office', client => {
      test('should go to the Front Office', () => client.accessToFO(AccessPageFO));
    }, 'common_client');
    commonScenarios.checkContactFO(contactData, true);
  }, 'common_client');
  scenario('Test 4: Delete a "Contact" with bulk action in the Back Office and check it in the Front Office', () => {
    scenario('Go back to the Back Office', client => {
      test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
    }, 'common_client');
    commonScenarios.createContact(JSON.parse(JSON.stringify(contactData)));
    commonScenarios.deleteContactWithBulkAction(contactData.title);
    scenario('Go to the Front Office', client => {
      test('should go to the Front Office', () => client.accessToFO(AccessPageFO));
    }, 'common_client');
    commonScenarios.checkContactFO(contactData, true);
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
