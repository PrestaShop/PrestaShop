const {AccessPageBO} = require('../../../selectors/BO/access_page');
const common_scenarios = require('./customer');

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

scenario('Create "Customer"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'customer');

  common_scenarios.createCustomer(customerData);
  common_scenarios.checkCustomerBO(customerData);

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'customer');
}, 'customer', true);
