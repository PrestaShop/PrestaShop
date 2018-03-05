const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('../../../common_scenarios/customer');

let customerData = {
  first_name: 'demo',
  last_name: 'demo',
  email_address: 'demo@prestashop.com',
  password: '123456789',
  birthday: {
    day: '18',
    month: '12',
    year: '1991'
  }
};

let editCustomerData = {
  first_name: 'customerFirstName',
  last_name: 'customerLastName',
  email_address: 'customerEmail@prestashop.com',
  password: '123456789',
  birthday: {
    day: '31',
    month: '3',
    year: '1994'
  }
};

require('../../../high/09_customer/1_create_customer');

scenario('Edit, delete and delete with bulk actions "Customer"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'customer');

  common_scenarios.editCustomer(customerData.email_address, editCustomerData);
  common_scenarios.checkCustomerBO(editCustomerData);
  common_scenarios.deleteCustomer(editCustomerData.email_address);
  common_scenarios.createCustomer(editCustomerData);
  common_scenarios.deleteCustomerWithBulkActions(editCustomerData.email_address);

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'customer');
}, 'customer', true);
