const {AccessPageBO} = require('../../../selectors/BO/access_page');

const common_scenarios = require('../../common_scenarios/addresses');

let addressData = {
  email: 'pub@prestashop.com',
  id_number: '123456789',
  address_alias: 'Ma super address',
  first_name: 'John',
  last_name: 'DOE',
  company: 'prestashop',
  vat_number: '0123456789',
  address: '12 rue d\'amsterdam',
  second_address: 'RDC',
  ZIP: '75009',
  city: 'Paris',
  country: 'France',
  home_phone: '0123456789',
  other: 'azerty'
};

scenario('Create "Address"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'customer');

  common_scenarios.createAddress(addressData);
  common_scenarios.checkAddressBO(addressData);

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'customer');
}, 'customer', true);
