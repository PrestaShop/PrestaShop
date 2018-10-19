const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {accountPage}= require('../../../selectors/FO/add_account_page');
let data = require('./../../../datas/customer_and_address_data');

scenario('Create a customer account in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should access to the Front Office', () => client.accessToFO(AccessPageFO));
    test('should change the Front Office language to "English"', () => client.changeLanguage());
    test('should click on the "Sign in" link', () => client.waitForExistAndClick(AccessPageFO.sign_in_button));
    test('should click on "No account? Create one here" link', () => client.waitForExistAndClick(accountPage.create_button));
    test('should choose a "Social title" option', () => client.waitForExistAndClick(accountPage.gender_radio_button));
    test('should set the "First name" input', () => client.waitAndSetValue(accountPage.firstname_input, data.customer.firstname));
    test('should set the "Last name" input', () => client.waitAndSetValue(accountPage.lastname_input, data.customer.lastname));
    test('should set the "Email" input', () => client.waitAndSetValue(accountPage.email_input, 'new' + data.customer.email.replace("%ID", date_time)));
    test('should set the "Password" input', () => client.waitAndSetValue(accountPage.password_input, data.customer.password));
    test('should click on "Save" button', () => client.waitForExistAndClick(accountPage.save_account_button));
}, 'common_client');

scenario('Check the creation of customer account', client => {
    test('should "Sign out"', () => client.signOutFO(AccessPageFO));
    test('should change the Front Office language to "English"', () => client.changeLanguage());
    test('should login successfully with the created account', () => client.waitForExistAndClick(AccessPageFO.sign_in_button));
    test('should set the "Email" input', () => client.waitAndSetValue(accountPage.email_input, 'new' + data.customer.email.replace("%ID", date_time)));
    test('should set the "Password" input', () => client.waitAndSetValue(accountPage.password_input, data.customer.password));
    test('should click on "SIGN IN" button', () => client.waitForExistAndClick(AccessPageFO.login_button));
}, 'common_client');

scenario('Create "Address"', client => {
    test('should click on "ADD FIRST ADDRESS" button', () => client.waitForExistAndClick(accountPage.add_first_address));
    test('should set the "Address" input', () => client.waitAndSetValue(accountPage.adr_address, data.address.address));
    test('should set the "Zip/Postal Code" input', () => client.waitAndSetValue(accountPage.adr_postcode, data.address.postalCode));
    test('should set the "City" input', () => client.waitAndSetValue(accountPage.adr_city, data.address.city));
    test('should click on "SAVE" button', () => client.waitForExistAndClick(accountPage.adr_save));
    test('should check that the success alert message is well displayed', () => client.checkTextValue(accountPage.save_notification, 'Address successfully added!'));
}, 'common_client');

scenario('Check the creation of the address', client => {
    test('should click on "update" link', () => client.waitForExistAndClick(accountPage.adr_update));
    test('should check that the "First name" of customer is equal to "John"', () => client.checkAttributeValue(accountPage.firstname_input, 'value', data.customer.firstname));
    test('should check that the "Last name" of customer is equal to "Doe"', () => client.checkAttributeValue(accountPage.lastname_input, 'value', data.customer.lastname));
    test('should check that the "Address" of customer is equal to "16, Main street"', () => client.checkAttributeValue(accountPage.adr_address, 'value', data.address.address));
    test('should check that the "Zip/Postal Code" of customer is equal to "75002"', () => client.checkAttributeValue(accountPage.adr_postcode, 'value', data.address.postalCode));
    test('should check that the "City" of customer is equal to "Paris"', () => client.checkAttributeValue(accountPage.adr_city, 'value', data.address.city));
    test('should go back to the home page', () => client.waitForExistAndClick(AccessPageFO.logo_home_page));
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
}, 'common_client', true);