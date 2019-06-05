const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Addresses} = require('../../../selectors/BO/customers/addresses');

const common_scenarios = require('../../common_scenarios/address');

let customerData = {
  first_name: 'demo',
  last_name: 'demo',
  email_address: 'demo@prestashop.com',
};

scenario('Create "Address"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'customer');

  common_scenarios.createCustomerAddress(customerData);

  scenario('Check the address creation', client => {
    test('should check the existence of the filter address input', () => client.isVisible(Addresses.filter_by_address_input));
    test('should search the customer by address', () => client.searchByAddress(Addresses, date_time));
  }, 'customer');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'customer');
}, 'customer', true);
