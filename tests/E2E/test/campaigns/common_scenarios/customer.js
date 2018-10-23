const {Menu} = require('../../selectors/BO/menu.js');
const {Customer} = require('../../selectors/BO/customers/customer');
const {accountPage} = require('../../selectors/FO/add_account_page');
const {BO} = require('../../selectors/BO/customers/index');

let promise = Promise.resolve();

/****Example of customer data ****
 * let customerData = {
 *  first_name: 'demo',
 *  last_name: 'demo',
 *  email_address: 'demo@prestashop.com',
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
      test('should set the "Email" input', () => client.waitAndSetValue(Customer.email_address_input, date_time + customerData.email_address));
      test('should set the "Password" input', () => client.waitAndSetValue(Customer.password_input, customerData.password));
      test('should set the customer "Birthday"', () => {
        return promise
          .then(() => client.waitAndSelectByValue(Customer.days_select, customerData.birthday.day))
          .then(() => client.waitAndSelectByValue(Customer.month_select, customerData.birthday.month))
          .then(() => client.waitAndSelectByValue(Customer.years_select, customerData.birthday.year));
      });
      test('should activate "Partner offers" option ', () => client.waitForExistAndClick(Customer.Partner_offers));
      test('should click on "Save" button', () => client.waitForExistAndClick(Customer.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful creation.'));
    }, 'customer');
  },
  checkCustomerBO: function (customerData) {
    scenario('Check the customer creation in the Back Office', client => {
      test('should check the email existence in the "Customers list"', () => {
        return promise
          .then(() => client.isVisible(Customer.customer_filter_by_email_input))
          .then(() => client.search(Customer.customer_filter_by_email_input, date_time + customerData.email_address))
          .then(() => client.checkExistence(Customer.email_address_value, date_time + customerData.email_address, 6));
      });
    }, 'customer');
  },
  /**
   * This function allows you to search for a customer by his email and edit it
   * @param customerEmail customer mail address
   * @param editCustomerData : customer new data
   * @returns {*}
   */
  editCustomer: function (customerEmail, editCustomerData) {
    scenario('Edit Customer', client => {
      test('should go to the "Customers" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.customers_submenu));
      test('should search for the customer email in the "Customers list"', () => {
        return promise
          .then(() => client.isVisible(Customer.customer_filter_by_email_input))
          .then(() => client.search(Customer.customer_filter_by_email_input, date_time + customerEmail));
      });
      test('should click on "Edit" button', () => client.waitForExistAndClick(Customer.edit_button));
      test('should choose the "Social title" radio', () => client.waitForExistAndClick(Customer.social_title_button));
      test('should set the new "First name" input', () => client.waitAndSetValue(Customer.first_name_input, editCustomerData.first_name));
      test('should set the new "Last name" input', () => client.waitAndSetValue(Customer.last_name_input, editCustomerData.last_name));
      test('should set the new "Email" input', () => client.waitAndSetValue(Customer.email_address_input, date_time + editCustomerData.email_address));
      test('should set the new "Password" input', () => client.waitAndSetValue(Customer.password_input, editCustomerData.password));
      test('should set the new customer "Birthday"', () => {
        return promise
          .then(() => client.waitAndSelectByValue(Customer.days_select, editCustomerData.birthday.day))
          .then(() => client.waitAndSelectByValue(Customer.month_select, editCustomerData.birthday.month))
          .then(() => client.waitAndSelectByValue(Customer.years_select, editCustomerData.birthday.year));
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
          .then(() => client.search(Customer.customer_filter_by_email_input, date_time + customerEmail));
      });
      test('should click on "Delete" button', () => {
        return promise
          .then(() => client.scrollWaitForExistAndClick(Customer.dropdown_toggle, 50, 2000))
          .then(() => client.waitForExistAndClick(Customer.delete_button, 1000));
      });
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should choose the option that allows customers to register again with the same email address', () => client.waitForExistAndClick(Customer.delete_first_option));
      test('should click on "Delete" button', () => client.waitForExistAndClick(Customer.delete_confirmation_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful deletion.', 'equal', 2000));
    }, 'customer');
  },
  deleteCustomerWithBulkActions: function (customerEmail) {
    scenario('Delete customer with bulk actions', client => {
      test('should go to the "Customers" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.customers_submenu));
      test('should search for the customer email in the "Customers list"', () => {
        return promise
          .then(() => client.isVisible(Customer.customer_filter_by_email_input))
          .then(() => client.search(Customer.customer_filter_by_email_input, date_time + customerEmail));
      });
      test('should select the searched client', () => client.waitForExistAndClick(Customer.select_customer));
      test('should click on the "Bulk actions" button', () => client.waitForExistAndClick(Customer.bulk_actions_button));
      test('should click on the "Delete selected" button', () => client.waitForExistAndClick(Customer.bulk_actions_delete_button));
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should choose the option that allows customers to register again with the same email address', () => client.waitForExistAndClick(Customer.delete_first_option));
      test('should click on "Delete" button', () => client.waitForExistAndClick(Customer.delete_confirmation_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nThe selection has been successfully deleted.'));
    }, 'customer');
  },
  checkCustomerFO: function (client, customerData) {
    test('should check the customer "First name"', () => client.checkAttributeValue(accountPage.firstname_input, 'value', customerData.first_name));
    test('should check the customer "Last name"', () => client.checkAttributeValue(accountPage.lastname_input, 'value', customerData.last_name));
    test('should check that the customer "Email" is equal to "' + date_time + customerData.email_address + '"', () => client.checkAttributeValue(accountPage.email_input, 'value', date_time + customerData.email_address));
    test('should check that the customer "Birthday" is equal to "' + customerData.birthday.month + '/' + customerData.birthday.day + '/' + customerData.birthday.year + '"', () => client.checkAttributeValue(accountPage.birthday_input, 'value', customerData.birthday.month + '/' + customerData.birthday.day + '/' + customerData.birthday.year, "contain"));
  }
};
