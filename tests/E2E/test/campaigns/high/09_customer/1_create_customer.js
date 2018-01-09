const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Customer} = require('../../../selectors/BO/customers/customer');
const {BO} = require('../../../selectors/BO/customers/index');
let promise = Promise.resolve();

scenario('Create "Customer"', () => {
    scenario('Login in the Back Office', client => {
        test('should open the browser', () => client.open());
        test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'customer');
    scenario('Create a new "Customer"', client => {
        test('should go to the "Customers" page', () => client.goToSubtabMenuPage(Customer.customer_menu, Customer.customers_subtab));
        test('should click on "Add new customer" button', () => client.waitForExistAndClick(Customer.new_customer_button));
        test('should choose the "Social title" radio', () => client.waitForExistAndClick(Customer.social_title_button));
        test('should set the "First name" input', () => client.waitAndSetValue(Customer.first_name_input, 'John'));
        test('should set the "Last name" input', () => client.waitAndSetValue(Customer.last_name_input, 'Doe'));
        test('should set the "Email" input', () => client.waitAndSetValue(Customer.email_address_input, 'demo' + date_time + '@prestashop.com'));
        test('should set the "Password" input', () => client.waitAndSetValue(Customer.password_input, '123456789'));
        test('should set the customer "Birthday"', () => {
            return promise
                .then(() => client.waitAndSelectByValue(Customer.days_select, '18'))
                .then(() => client.waitAndSelectByValue(Customer.month_select, '12'))
                .then(() => client.waitAndSelectByValue(Customer.years_select, '1991'))
        });
        test('should click on "Save" button', () => client.waitForExistAndClick(Customer.save_button));
        test('should verify the appearance of the green validation', () => client.checkTextValue(BO.success_panel, '×\nSuccessful creation.'));
    }, 'customer');
    scenario('Check the customer creation', client => {
        test('should check the existence of the customer filter email input', () => client.isVisible(Customer.customer_filter_by_email_input));
        test('should search the customer by email', () => client.searchByEmail(Customer, 'demo' + date_time + '@prestashop.com'));
    }, 'customer');
    scenario('Logout from the Back Office', client => {
        test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'customer');
}, 'customer', true);