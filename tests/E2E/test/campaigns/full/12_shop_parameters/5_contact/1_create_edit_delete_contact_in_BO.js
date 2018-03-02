const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('../../../common_scenarios/contact');

let contactData = {
  title: 'Service',
  email: 'john.doe@prestashop.com',
  description: 'To contact the administration service'
};

scenario('Create, edit, delete and check "Contact" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_scenarios.createContact(contactData);
  common_scenarios.checkContact(contactData);
  common_scenarios.editContact(contactData);
  common_scenarios.checkContact(contactData);
  common_scenarios.deleteContact(contactData);
  common_scenarios.createContact(JSON.parse(JSON.stringify(contactData)));
  common_scenarios.deleteContactWithBulkAction(contactData.title);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);