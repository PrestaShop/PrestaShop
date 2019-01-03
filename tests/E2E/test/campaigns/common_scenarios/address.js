/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
const {Menu} = require('../../selectors/BO/menu.js');
const {Addresses} = require('../../selectors/BO/customers/addresses');
const {BO} = require('../../selectors/BO/customers/index');

module.exports = {
  createCustomerAddress: function (customerData) {
    scenario('Create a new "Address"', client => {
      test('should go to the "Customers" page', () => client.goToSubtabMenuPage(Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.addresses_submenu));
      test('should click on add new address', () => client.waitForExistAndClick(Addresses.new_address_button));
      test('should set "Email" input', () => client.waitAndSetValue(Addresses.email_input, date_time+customerData.email_address));
      test('should set "Identification number" input', () => client.waitAndSetValue(Addresses.id_number_input, '0123456789'));
      test('should set "Address alias" input', () => client.waitAndSetValue(Addresses.address_alias_input, 'Ma super addresse'));
      test('should check that the "First name" input is "'+customerData.first_name+'"', () => client.checkAttributeValue(Addresses.first_name_input, 'value', customerData.first_name));
      test('should check that the "Last name" input is "'+customerData.last_name+'"', () => client.checkAttributeValue(Addresses.last_name_input, 'value', customerData.last_name));
      test('should set "Company" input', () => client.waitAndSetValue(Addresses.company, 'Presta'));
      test('should set "VAT number" input', () => client.waitAndSetValue(Addresses.VAT_number_input, '0123456789'));
      test('should set "Address" input', () => client.waitAndSetValue(Addresses.address_input, "12 rue d'amsterdam" + date_time));
      test('should set "Second address" input', () => client.waitAndSetValue(Addresses.address_second_input, "RDC"));
      test('should set "Postal code" input', () => client.waitAndSetValue(Addresses.zip_code_input, "75009"));
      test('should set "City" input', () => client.waitAndSetValue(Addresses.city_input, "Paris"));
      test('should set "Pays" input', () => client.waitAndSelectByValue(Addresses.country_input, "8"));
      test('should set "Home phone" input', () => client.waitAndSetValue(Addresses.phone_input, "0123456789"));
      test('should set "Other information" input', () => client.waitAndSetValue(Addresses.other_input, "azerty"));
      test('should click on "Save" button', () => client.scrollWaitForExistAndClick(Addresses.save_button, 50));
      test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful creation.'));
    }, 'customer');
  }
};
