const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('../../../high/09_customer/customer');

let customerData = {
  first_name: 'demo',
  last_name: 'demo',
  email_address: 'demo',
  password: '123456789',
  birthday: {
    day: '18',
    month: '12',
    year: '1991'
  }
};

let editCustomerData = {
  first_name: 'CustomerFirstName',
  last_name: 'CustomerFirstName',
  email_address: 'CustomerFirstName',
  password: '123456789',
  birthday: {
    day: '31',
    month: '3',
    year: '1994'
  }
};

require('../../../high/09_customer/1_create_customer');

scenario('Edit "Customer"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'customer');

common_scenarios.editCustomer(customerData.email_address, editCustomerData);

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'customer');
}, 'customer', true);
