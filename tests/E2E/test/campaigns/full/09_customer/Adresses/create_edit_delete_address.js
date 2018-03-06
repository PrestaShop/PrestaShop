const {AccessPageBO} = require('../../../../selectors/BO/access_page');

const common_scenarios = require('../../../high/09_customer/addresses');

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
  Country: 'France',
  home_phone: '0123456789',
  other: 'azerty'
};

let editAddressData = {
  email: 'pub@prestashop.com',
  id_number: '987654321',
  address_alias: 'Ma super new address',
  first_name: 'John',
  last_name: 'DOE',
  company: 'prestashop',
  vat_number: '9876543210',
  address: '12 rue de paris',
  second_address: 'RDC',
  ZIP: '75009',
  city: 'Paris',
  Country: 'France',
  home_phone: '9876543210',
  other: 'azerty'
};

require('../../../high/09_customer/2_create_address');

scenario('Edit "Address"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'customer');

  common_scenarios.editAddress(addressData, editAddressData);

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'customer');
}, 'customer', true);