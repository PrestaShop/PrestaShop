const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {Addresses} = require('../../../../selectors/BO/customers/addresses');
const {Menu} = require('../../../../selectors/BO/menu.js');
const common_scenarios = require('../../../common_scenarios/addresses');
const customer_common_scenarios = require('../../../common_scenarios/customer');
const {AccessPageFO} = require('../../../../selectors/FO/access_page');
const {BO} = require('../../../../selectors/BO/customers/index');
const welcomeScenarios = require('../../../common_scenarios/welcome');

let promise = Promise.resolve();

let customerData = {
  first_name: 'demo',
  last_name: 'demo',
  email_address: global.adminEmail,
  password: '123456789',
  birthday: {
    day: '18',
    month: '12',
    year: '1991'
  }
};

let addressData = {
  email: global.adminEmail,
  id_number: '123456789',
  address_alias: 'Ma super address',
  first_name: 'demo',
  last_name: 'demo',
  company: 'prestashop',
  vat_number: '0123456789',
  address: '12 rue d\'amsterdam',
  second_address: 'RDC',
  ZIP: '75009',
  city: 'Paris',
  country: 'France',
  home_phone: '0123456789',
  other: 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.'
};

let editAddressData = {
  email: 'pub@prestashop.com',
  id_number: '987654321',
  address_alias: 'Ma super new address',
  first_name: 'demo',
  last_name: 'demo',
  company: 'prestashop',
  vat_number: '9876543210',
  address: '125 rue de marseille',
  second_address: 'RDC',
  ZIP: '75500',
  city: 'Marseille',
  country: 'France',
  home_phone: '9876543210',
  mobile_phone: '9876543210',
  other: 'azerty'
};

scenario('Login in the Back Office', client => {
  test('should open the browser', () => client.open());
  test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
}, 'customer');
welcomeScenarios.findAndCloseWelcomeModal();
customer_common_scenarios.createCustomer(customerData);
scenario('Edit, delete and delete with bulk actions "Address"', () => {

  scenario('Go to "Addresses" page', client => {
    test('should go to the "Addresses" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.addresses_submenu));
    test('should click on "Add new address" button', () => client.waitForExistAndClick(Addresses.new_address_button));
  }, 'customer');

  common_scenarios.checkAddressRequiredInput(addressData, 'first');
  common_scenarios.checkAddressBO(addressData);

  scenario('Change the required fields addresses parameter', client => {
    test('should click on "Set required fields for this section" button', () => client.waitForExistAndClick(Addresses.required_fields_button));
    test('should click on "company" check box button', () => client.waitForVisibleAndClick(Addresses.company_field_input));
    test('should click on "Save" button', () => client.waitForVisibleAndClick(Addresses.submit_field));
    test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful update.'));
  }, 'customer');

  scenario('Add new address', client => {
    test('should click on "Add new address" button', () => client.waitForExistAndClick(Addresses.new_address_button));
  }, 'customer');

  common_scenarios.checkAddressRequiredInput(addressData, 'second');

  scenario('Change the required fields addresses parameter', client => {
    test('should click on "Set required fields for this section" button', () => client.waitForExistAndClick(Addresses.required_fields_button));
    test('should check all fields names', () => client.waitForVisibleAndClick(Addresses.select_all_field_name));
    test('should click on "Save" button', () => client.waitForVisibleAndClick(Addresses.submit_field));
  }, 'customer');

  scenario('Add new address', client => {
    test('should click on "Add new address" button', () => client.waitForExistAndClick(Addresses.new_address_button));
  }, 'customer');

  common_scenarios.checkAddressRequiredInput(addressData, 'allInput');

  scenario('Disable all required field addresses parameter', client => {
    test('should go to the "Addresses" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.addresses_submenu));
    test('should click on "Set required fields for this section" button', () => client.waitForExistAndClick(Addresses.required_fields_button));
    test('should disable all fields names', () => {
      return promise
        .then(() => client.waitForVisibleAndClick(Addresses.select_all_field_name))
        .then(() => client.waitForVisibleAndClick(Addresses.select_all_field_name));
    });
    test('should click on "Save" button', () => client.waitForVisibleAndClick(Addresses.submit_field));
  }, 'customer');

  scenario('Delete the second address created', client => {
    test('should check the address existence in the "addresses list"', () => {
      return promise
        .then(() => client.isVisible(Addresses.filter_by_address_input))
        .then(() => client.search(Addresses.filter_by_address_input, addressData.address + " " + date_time));
    });
    test('Should click on "Delete" button of the second address', () => {
      return promise
        .then(() => client.waitForVisibleAndClick(Addresses.dropdown_toggle))
        .then(() => client.waitForVisibleAndClick(Addresses.delete_button))
        .then(() => client.alertAccept());
    });
    test('should get the address ID', () => client.getTextInVar(Addresses.address_id, 'address_id'));
  }, 'customer');

}, 'customer');

scenario('Edit address', client => {
  scenario('Check customer address in the Front Office', client => {
    test('should open the Front Office in new window', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1));
    });
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should click on "sign in" button', () => client.waitForExistAndClick(AccessPageFO.sign_in_button));
    test('should set the "Email" input', () => client.waitAndSetValue(AccessPageFO.login_input, date_time + customerData.email_address));
    test('should set the "Password" input', () => client.waitAndSetValue(AccessPageFO.password_inputFO, customerData.password));
    test('should click on "Sign In" button', () => client.waitForExistAndClick(AccessPageFO.login_button));
    test('should click on "Addresses" button', () => client.waitForExistAndClick(AccessPageFO.address_information_link));
    test('should check "Address" information', () => {
      return promise
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), addressData.first_name + " " + addressData.last_name, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), addressData.company, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), addressData.address + " " + date_time, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), addressData.second_address, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), addressData.ZIP + " " + addressData.city, "contain"))
        .then(() => client.switchWindow(0));
    });
    common_scenarios.editAddress(addressData.address, editAddressData);
  }, 'customer');

  scenario('Check the edited address in the Back Office', client => {
    test('should go to the Front Office and refresh the page', () => {
      return promise
        .then(() => client.switchWindow(1))
        .then(() => client.refresh());
    });
    test('should check "Address" informations', () => {
      return promise
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), editAddressData.first_name + " " + editAddressData.last_name, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), editAddressData.company, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), editAddressData.address + " " + date_time, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), editAddressData.second_address, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), editAddressData.ZIP + " " + editAddressData.city, "contain"))
        .then(() => client.switchWindow(0));
    });
  }, 'customer');
}, 'customer');

scenario('Delete address', client => {
  common_scenarios.deleteAddress(editAddressData.address);
  scenario('Check that no results appear', client => {
    test('should Check that no results appear', () => client.isExisting(Addresses.empty_class));
  }, 'customer');
  scenario('Check the deleted address in the Front Office', client => {
    test('should check that the address has been deleted in the Front Office', () => {
      return promise
        .then(() => client.switchWindow(1))
        .then(() => client.refresh());
    });
    test('should Check that no results appear', () => {
      return promise
        .then(() => client.checkTextValue(AccessPageFO.addresses_warning, 'No addresses are available.', "contain"))
        .then(() => client.switchWindow(0));
    });
  }, 'customer');
}, 'customer');

scenario('Delete address with bulk actions', client => {
  common_scenarios.createAddress(addressData);
  common_scenarios.checkAddressBO(addressData);

  scenario('Get the address ID', client => {
    test('should get the address ID', () => client.getTextInVar(Addresses.address_id, 'address_id'));
  }, 'customer');

  scenario('Check the created address in the Front Office', client => {
    test('should check that the address has been created in the Front Office', () => {
      return promise
        .then(() => client.switchWindow(1))
        .then(() => client.refresh());
    });
    test('should check "Address" information', () => {
      return promise
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), addressData.first_name + " " + addressData.last_name, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), addressData.company, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), addressData.address + " " + date_time, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), addressData.second_address, "contain"))
        .then(() => client.checkTextValue(AccessPageFO.address_information.replace('%ID', global.tab['address_id']), addressData.ZIP + " " + addressData.city, "contain"))
        .then(() => client.switchWindow(0));
    });
  }, 'customer');

  common_scenarios.deleteAddressWithBulkActions(addressData.address);

  scenario('Check the deleted address in the Front Office', client => {
    test('should check that the address has been deleted in the Front Office', () => {
      return promise
        .then(() => client.switchWindow(1))
        .then(() => client.refresh());
    });
    test('should Check that no results appear', () => {
      return promise
        .then(() => client.checkTextValue(AccessPageFO.addresses_warning, 'No addresses are available.', "contain"))
        .then(() => client.switchWindow(0));
    });
  }, 'customer');
}, 'customer');

scenario('Logout from the Back Office', client => {
  test('should logout successfully from the Back Office', () => client.signOutBO());
}, 'customer', true);
