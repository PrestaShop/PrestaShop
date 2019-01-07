const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../../selectors/FO/access_page');
const {accountPage} = require('../../../../selectors/FO/add_account_page');
const {Menu} = require('../../../../selectors/BO/menu.js');
const {Customer} = require('../../../../selectors/BO/customers/customer');
const {Addresses} = require('../../../../selectors/BO/customers/addresses');
const {BO} = require('../../../../selectors/BO/customers/index');
const common_scenarios = require('../../../common_scenarios/customer');
const common_scenarios_address = require('../../../common_scenarios/address');
let promise = Promise.resolve();

let customerData = {
  first_name: 'demo',
  last_name: 'demo',
  email_address: 'demo' + global.adminEmail,
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
  email_address: 'customeremail@prestashop.com',
  password: '123456789',
  birthday: {
    day: '31',
    month: '10',
    year: '1994'
  }
};

scenario('Create, Edit, delete "Customer"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'customer');

  common_scenarios.createCustomer(customerData);
  common_scenarios.checkCustomerBO(customerData);

  scenario('Check the customer creation in the Front Office', client => {
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
    test('should click on "Information" button', () => client.waitForExistAndClick(AccessPageFO.identity_link));
    common_scenarios.checkCustomerFO(client, customerData);
    test('should go to the Back Office', () => client.switchWindow(0));
  }, 'customer');

  common_scenarios.editCustomer(customerData.email_address, editCustomerData);
  common_scenarios.checkCustomerBO(editCustomerData);

  scenario('Check that the customer information is updated in the Front Office', client => {
    test('should go to the Front Office', () => client.switchWindow(1));
    test('should refresh the page', () => client.refresh());
    common_scenarios.checkCustomerFO(client, editCustomerData);
    test('should go to the Back Office', () => client.switchWindow(0));
  }, 'customer');

  common_scenarios_address.createCustomerAddress(editCustomerData);
  scenario('Check the address creation', client => {
    test('should check the existence of the filter address input', () => client.isVisible(Addresses.filter_by_address_input));
    test('should search the customer by address', () => client.searchByAddress(Addresses, date_time));
  }, 'customer');
  scenario('Open addresses menu in a new window', client => {
    test('should open the menu "Customers - Addresses"', () => client.middleClick(Menu.Sell.Customers.addresses_submenu));
  }, 'customer');

  common_scenarios.deleteCustomer(editCustomerData.email_address);

  scenario('Check the customer deletion in the Back Office', client => {
    test('should go to "Customers" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.customers_submenu));
    test('should search for the customer email in the "Customers list"', () => {
      return promise
        .then(() => client.isVisible(Customer.customer_filter_by_email_input))
        .then(() => client.search(Customer.customer_filter_by_email_input, date_time + editCustomerData.email_address));
    });
    test('should check that there is no result', () => client.isExisting(Customer.empty_list_icon));
  }, 'customer');

  scenario('Verify that the address related to the deleted customer doesn\'t exist', client => {
    test('should go to "Addresses" page', () => client.switchWindow(2));
    test('should refresh the page', () => client.refresh());
    test('should check that the deleted customer address doesn\'t exist', () => client.isNotExisting(Customer.customer_link.replace('%ID', date_time)));
    test('should go to Customers page', () => client.switchWindow(1));
  }, 'customer');

  scenario('Check the ability to create the same deleted customer from the Front Office', client => {
    test('should click on shop logo', () => client.waitForExistAndClick(AccessPageFO.logo_home_page));
    test('should set the language of shop to "English"', () => client.changeLanguage());
    test('should click on "Sign In" button', () => client.waitForExistAndClick(AccessPageFO.sign_in_button, 2000));
    test('should click on "No account? Create one here" button', () => client.waitForExistAndClick(AccessPageFO.create_account_button, 2000));
    test('should check "Mrs" radio button', () => client.waitForExistAndClick(accountPage.gender_radio_button));
    test('should set the "First Name" input', () => client.waitAndSetValue(accountPage.firstname_input, editCustomerData.first_name));
    test('should set the "Last Name" input', () => client.waitAndSetValue(accountPage.lastname_input, editCustomerData.last_name));
    test('should set the "Email" input', () => client.waitAndSetValue(accountPage.email_input, date_time + editCustomerData.email_address));
    test('should set the "Password" input', () => client.waitAndSetValue(accountPage.password_input, editCustomerData.password));
    test('should set the "Birthday" input', () => client.waitAndSetValue(accountPage.birthday_input, editCustomerData.birthday.month + '/' + editCustomerData.birthday.day + '/' + editCustomerData.birthday.year));
    test('should click on "Save" button', () => client.waitForExistAndClick(accountPage.save_account_button));
  }, 'customer');

  scenario('Delete the created customer ', client => {
    test('should go to "Addresses" page', () => client.switchWindow(0));
    test('should go to "Customers" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.customers_submenu));
    test('should search for the customer email in the "Customers list"', () => {
      return promise
        .then(() => client.isVisible(Customer.customer_filter_by_email_input))
        .then(() => client.search(Customer.customer_filter_by_email_input, date_time + editCustomerData.email_address));
    });
    test('should click on "Delete" button', () => {
      return promise
        .then(() => client.scrollWaitForExistAndClick(Customer.dropdown_toggle, 50, 1000))
        .then(() => client.waitForExistAndClick(Customer.delete_button, 1000));
    });
    test('should accept the currently displayed alert dialog', () => client.alertAccept());
    test('should choose the option that Doesn\'t allows customers to register again with the same email address', () => client.waitForExistAndClick(Customer.delete_second_option));
    test('should click on "Delete" button', () => client.waitForExistAndClick(Customer.delete_confirmation_button, 2000));
    test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful deletion.', 'equal', 2000));
    test('should go to the Front Office', () => client.switchWindow(1));
  }, 'customer');

  scenario('Check the ability to create the same deleted customer from the Front Office', client => {
    test('should click on shop logo', () => client.waitForExistAndClick(AccessPageFO.logo_home_page));
    test('should set the language of shop to "English"', () => client.changeLanguage());
    test('should click on "Sign In" button', () => client.waitForExistAndClick(AccessPageFO.sign_in_button));
    test('should click on "No account? Create one here" button', () => client.waitForExistAndClick(AccessPageFO.create_account_button));
    test('should set the "Social title" to Mrs', () => client.waitForExistAndClick(accountPage.gender_radio_button));
    test('should set the "First Name" input', () => client.waitAndSetValue(accountPage.firstname_input, editCustomerData.first_name));
    test('should set the "Last Name" input', () => client.waitAndSetValue(accountPage.lastname_input, editCustomerData.last_name));
    test('should set the "Email" input', () => client.waitAndSetValue(accountPage.email_input, date_time + editCustomerData.email_address));
    test('should set the "Password" input', () => client.waitAndSetValue(accountPage.password_input, editCustomerData.password));
    test('should set the "Birthday" input', () => client.waitAndSetValue(accountPage.birthday_input, editCustomerData.birthday.month + '/' + editCustomerData.birthday.day + '/' + editCustomerData.birthday.year));
    test('should click on the "Save" button', () => client.waitForExistAndClick(accountPage.save_account_button));
    test('should check that the warning message appears', () => client.checkTextValue(accountPage.danger_alert, 'is already used', 'contain'));
    test('should go to the Back Office', () => client.switchWindow(0));
  }, 'customer');

  common_scenarios.createCustomer(editCustomerData);
  common_scenarios.deleteCustomerWithBulkActions(editCustomerData.email_address);

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'customer');
}, 'customer', true);
