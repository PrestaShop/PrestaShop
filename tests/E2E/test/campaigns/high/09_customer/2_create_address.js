const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Customer} = require('../../../selectors/BO/customers/customer');
const {Addresses} = require('../../../selectors/BO/customers/addresses');
const {BO} = require('../../../selectors/BO/customers/index');

scenario('Create "Address"', () => {
    scenario('Login in the Back Office', client => {
        test('should open the browser', () => client.open());
        test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'customer');
    scenario('Createa a new "Address"', client => {
        test('should go to the "Customers" page', () => client.goToSubtabMenuPage(Customer.customer_menu, Addresses.addresses_menu));
        test('should click on add new address', () => client.waitForExistAndClick(Addresses.new_address_button));
        test('should set "Email" input', () => client.waitAndSetValue(Addresses.email_input, 'demo' + date_time + '@prestashop.com'));
        test('should set "Identification number" input', () => client.waitAndSetValue(Addresses.id_number_input, '0123456789'));
        test('should set "Address alias" input', () => client.waitAndSetValue(Addresses.address_alias_input, 'Ma super addresse'));
        test('should check that the "First name" is "John"', () => client.checkAttributeValue(Addresses.first_name_input, 'value', 'John'));
        test('should check that the "Last name" is "Doe"', () => client.checkAttributeValue(Addresses.last_name_input, 'value', 'Doe'));
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
    scenario('Check the address creation', client => {
        test('should check the existence of the filter address input', () => client.isVisible(Addresses.filter_by_address_input));
        test('should search the customer by address', () => client.searchByAddress(Addresses, date_time));
    }, 'customer');
    scenario('Logout from the Back Office', client => {
        test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'customer');
}, 'customer', true);