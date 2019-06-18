/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-207"][Name="Create a new contact"]
 * [id="PS-208"][Name="Edit contact"]
 * [id="PS-209"][Name="Delete contact"]
 * [id="PS-210"][Name="Bulk actions"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const commonScenarios = require('../../common_scenarios/contact');
const welcomeScenarios = require('../../common_scenarios/welcome');

let contactData = {
  title: 'Service',
  email: 'prestotests@gmail.com',
  description: 'To contact the administration service'
}, messageData = {
  email: 'john.doe@prestashop.com',
  attachment: 'prestashop.png',
  message: 'Test send message'
};

/**
 * This script should be moved to the campaign full when this issue will be fixed
 * https://github.com/PrestaShop/PrestaShop/issues/9646
 **/
scenario('Create, edit, delete and check "Contact" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
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
