const {Menu} = require('../../../selectors/BO/menu.js');
const {Customer} = require('../../../selectors/BO/customers/customer');
const {BO} = require('../../../selectors/BO/customers/index');

let promise = Promise.resolve();

/****Exemple of customer data ****
 * let customerData = {
 *  first_name: 'demo',
 *  last_name: 'demo',
 *  email_address: 'demo',
 *  password: '123456789',
 *  birthday:{
 *      day:'18',
 *      month:'12',
 *      year:'1991'
 *  }
 * };
 */

module.exports = {
  createCustomer: function (customerData) {
    scenario('Create "Customer"', client => {
      test('should go to the "Customers" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.customers_submenu));
      test('should click on "Add new customer" button', () => client.waitForExistAndClick(Customer.new_customer_button));
      test('should choose the "Social title" radio', () => client.waitForExistAndClick(Customer.social_title_button));
      test('should set the "First name" input', () => client.waitAndSetValue(Customer.first_name_input, customerData.first_name));
      test('should set the "Last name" input', () => client.waitAndSetValue(Customer.last_name_input, customerData.last_name));
      test('should set the "Email" input', () => client.waitAndSetValue(Customer.email_address_input, customerData.email_address + date_time + '@prestashop.com'));
      test('should set the "Password" input', () => client.waitAndSetValue(Customer.password_input, customerData.password));
      test('should set the customer "Birthday"', () => {
        return promise
          .then(() => client.waitAndSelectByValue(Customer.days_select, customerData.birthday.day))
          .then(() => client.waitAndSelectByValue(Customer.month_select, customerData.birthday.month))
          .then(() => client.waitAndSelectByValue(Customer.years_select, customerData.birthday.year))
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(Customer.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful creation.'));
    }, 'customer');
  },
  checkCustomerBO: function (customerData) {
    scenario('Check the customer creation', client => {
      test('should check the email existence in the "Customers list"', () => {
        return promise
          .then(() => client.isVisible(Customer.customer_filter_by_email_input))
          .then(() => client.EmailSearch(Customer.customer_filter_by_email_input, customerData.email_address))
          .then(() => client.CheckEmailExistence(Customer, customerData.email_address))
      });
    }, 'customer');
  },
  editCustomer: function (customerEmail, editCustomerData) {
    scenario('Check the customer creation', client => {
      test('should go to the "Customers" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.customers_submenu));
      test('should search for the customer email in the "Customers list"', () => {
        return promise
          .then(() => client.isVisible(Customer.customer_filter_by_email_input))
          .then(() => client.EmailSearch(Customer.customer_filter_by_email_input, customerEmail))
      });
      test('should click on "Edit" button', () => client.waitForExistAndClick(Customer.edit_button));
      test('should choose the "Social title" radio', () => client.waitForExistAndClick(Customer.social_title_button));
      test('should set the "First name" input', () => client.waitAndSetValue(Customer.first_name_input, editCustomerData.first_name));
      test('should set the "Last name" input', () => client.waitAndSetValue(Customer.last_name_input, editCustomerData.last_name));
      test('should set the "Email" input', () => client.waitAndSetValue(Customer.email_address_input, editCustomerData.email_address + date_time + '@prestashop.com'));
      test('should set the "Password" input', () => client.waitAndSetValue(Customer.password_input, editCustomerData.password));
      test('should set the customer "Birthday"', () => {
        return promise
          .then(() => client.waitAndSelectByValue(Customer.days_select, editCustomerData.birthday.day))
          .then(() => client.waitAndSelectByValue(Customer.month_select, editCustomerData.birthday.month))
          .then(() => client.waitAndSelectByValue(Customer.years_select, editCustomerData.birthday.year))
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(Customer.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful update.'));
    }, 'customer');
  },
  deleteCustomer: function (customerEmail) {
    scenario('Delete customer', client => {
    test('should go to the "Customers" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.customers_submenu));
    test('should search for the customer email in the "Customers list"', () => {
      return promise
        .then(() => client.isVisible(Customer.customer_filter_by_email_input))
        .then(() => client.EmailSearch(Customer.customer_filter_by_email_input, customerEmail))
    });
    test('should click on "Delete" button', () => {
      return promise
        .then(() => client.waitForExistAndClick(Customer.dropdown_toggle))
        .then(() => client.waitForExistAndClick(Customer.delete_button))
    });
    test('should accepts the currently displayed alert dialog', () => client.alertAccept());
    test('should choose the option that allows customers to register again with the same email address', () => client.waitForExistAndClick(Customer.delete_first_option));
    test('should click on "Delete" button', () => client.waitForExistAndClick(Customer.delete_confirmation_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful deletion.'));
    }, 'customer');
  }
};
