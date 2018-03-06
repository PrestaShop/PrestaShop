const {Addresses} = require('../../selectors/BO/customers/addresses');
const {Menu} = require('../../selectors/BO/menu.js');
const {BO} = require('../../selectors/BO/customers/index');

let promise = Promise.resolve();

/****Example of address data ****
 * let addressData = {
 *  email: 'pub@prestashop.com',
 *  id_number: '123456789',
 *  address_alias: 'Ma super address',
 *  first_name: 'John',
 *  last_name: 'DOE',
 *  company: 'prestashop',
 *  vat_number: '0123456789',
 *  address: '12 rue d'amsterdam',
 *  second_address: 'RDC',
 *  ZIP: '75009',
 *  city: 'Paris',
 *  country: 'France',
 *  home_phone: '0123456789',
 *  other: 'azerty'
 * };
 */

module.exports = {
  createAddress: function (addressData) {
    scenario('Create a new "Address"', client => {
      test('should go to the "Addresses" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.addresses_submenu));
      test('should click on add new address', () => client.waitForExistAndClick(Addresses.new_address_button));
      test('should set "Email" input', () => client.waitAndSetValue(Addresses.email_input, addressData.email));
      test('should set "Identification number" input', () => client.waitAndSetValue(Addresses.id_number_input, addressData.id_number));
      test('should set "Address alias" input', () => client.waitAndSetValue(Addresses.address_alias_input, addressData.address_alias));
      test('should check that the "First name" is "John"', () => client.checkAttributeValue(Addresses.first_name_input, 'value', addressData.first_name));
      test('should check that the "Last name" is "Doe"', () => client.checkAttributeValue(Addresses.last_name_input, 'value', addressData.last_name));
      test('should set "Company" input', () => client.waitAndSetValue(Addresses.company, addressData.company));
      test('should set "VAT number" input', () => client.waitAndSetValue(Addresses.VAT_number_input, addressData.vat_number));
      test('should set "Address" input', () => client.waitAndSetValue(Addresses.address_input, addressData.address + " " + date_time));
      test('should set "Second address" input', () => client.waitAndSetValue(Addresses.address_second_input, addressData.second_address));
      test('should set "Postal code" input', () => client.waitAndSetValue(Addresses.zip_code_input, addressData.ZIP));
      test('should set "City" input', () => client.waitAndSetValue(Addresses.city_input, addressData.city));
      test('should set "Pays" input', () => client.waitAndSelectByVisibleText(Addresses.country_input, addressData.country));
      test('should set "Home phone" input', () => client.waitAndSetValue(Addresses.phone_input, addressData.home_phone));
      test('should set "Other information" input', () => client.waitAndSetValue(Addresses.other_input, addressData.other));
      test('should click on "Save" button', () => client.scrollWaitForExistAndClick(Addresses.save_button, 50));
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful creation.'));
    }, 'customer');
  },
  checkAddressBO: function (addressData) {
    scenario('Check the address creation', client => {
      test('should check the address existence in the "addresses list"', () => {
        return promise
          .then(() => client.isVisible(Addresses.filter_by_address_input))
          .then(() => client.search(Addresses.filter_by_address_input, addressData.address + " " + date_time))
          .then(() => client.checkExistence(Addresses.address_value, addressData.address + " " + date_time, 5));
      });
    }, 'customer');
  },
  /**
   * This function allows you to search for a address and edit it
   * @param dataAddress
   * @param newAddressData
   * @returns {*}
   */
  editAddress: function (dataAddress, newAddressData) {
    scenario('Check the Address creation', client => {
      test('should go to the "Addresses" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.addresses_submenu));
      test('should search for the address in the "Addresses list"', () => {
        return promise
          .then(() => client.isVisible(Addresses.filter_by_address_input))
          .then(() => client.search(Addresses.filter_by_address_input, dataAddress + " " + date_time));
      });
      test('should click on "Edit" button', () => client.waitForExistAndClick(Addresses.edit_button));
      test('should set the new "Identification number" input', () => client.waitAndSetValue(Addresses.id_number_input, newAddressData.id_number));
      test('should set the new "Address alias" input', () => client.waitAndSetValue(Addresses.address_alias_input, newAddressData.address_alias));
      test('should check that the "First name" is "John"', () => client.checkAttributeValue(Addresses.first_name_input, 'value', newAddressData.first_name));
      test('should check that the "Last name" is "Doe"', () => client.checkAttributeValue(Addresses.last_name_input, 'value', newAddressData.last_name));
      test('should set the new "Company" input', () => client.waitAndSetValue(Addresses.company, newAddressData.company));
      test('should set the new "VAT number" input', () => client.waitAndSetValue(Addresses.VAT_number_input, newAddressData.vat_number));
      test('should set the new "Address" input', () => client.waitAndSetValue(Addresses.address_input, newAddressData.address + " " + date_time));
      test('should set the new "Second address" input', () => client.waitAndSetValue(Addresses.address_second_input, newAddressData.second_address));
      test('should set the new "Postal code" input', () => client.waitAndSetValue(Addresses.zip_code_input, newAddressData.ZIP));
      test('should set the new "City" input', () => client.waitAndSetValue(Addresses.city_input, newAddressData.city));
      test('should set the new "Pays" input', () => client.waitAndSelectByVisibleText(Addresses.country_input, newAddressData.country));
      test('should set the new "Home phone" input', () => client.waitAndSetValue(Addresses.phone_input, newAddressData.home_phone));
      test('should set the new "Other information" input', () => client.waitAndSetValue(Addresses.other_input, newAddressData.other));
      test('should click on "Save" button', () => client.scrollWaitForExistAndClick(Addresses.save_button, 50));
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful update.'));
    }, 'customer');
  },
  deleteAddress: function (dataAddress) {
    scenario('Delete address', client => {
      test('should go to the "Addresses" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.addresses_submenu));
      test('should search for the address in the "Addresses list"', () => {
        return promise
          .then(() => client.isVisible(Addresses.filter_by_address_input))
          .then(() => client.search(Addresses.filter_by_address_input, dataAddress + " " + date_time));
      });
      test('should click on "Delete" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(Addresses.dropdown_toggle))
          .then(() => client.waitForExistAndClick(Addresses.delete_button));
      });
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful deletion.'));
    }, 'customer');
  },
  deleteAddressWithBulkActions: function (dataAddress) {
    scenario('Delete address with bulk actions', client => {
      test('should go to the "Addresses" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.addresses_submenu));
      test('should search for the address in the "Addresses list"', () => {
        return promise
          .then(() => client.isVisible(Addresses.filter_by_address_input))
          .then(() => client.search(Addresses.filter_by_address_input, dataAddress + " " + date_time));
      });
      test('should select the searched client', () => client.waitForExistAndClick(Addresses.select_address));
      test('should click on the "Bulk actions" button', () => client.waitForExistAndClick(Addresses.bulk_actions_button));
      test('should click on the "Delete selected" button', () => client.waitForExistAndClick(Addresses.bulk_actions_delete_button));
      test('should accept the currently displayed alert dialog', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nThe selection has been successfully deleted.'));
    }, 'customer');
  }
};