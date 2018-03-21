const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const common_scenarios = require('../../common_scenarios/employee');
const common_international_scenarios = require('../../common_scenarios/international');

let employeeData = [
  {
    firstname: 'Demo',
    lastname: "Prestashop",
    email: 'demo@prestashop.com',
    password: 'prestashop_demo',
    profile: '4',
    language: 'Tiếng Việt (Vietnamese)'
  },
  {
    firstname: 'Demo',
    lastname: "Prestashop",
    email: 'demo@prestashop.com',
    password: 'prestashop_demo',
    profile: '4',
    language: 'English (English)'
  }
];

scenario('Import a localization', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_international_scenarios.importLocalization('Vietnam');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);

scenario('Edit the connected employee profile in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_scenarios.editEmployee(employeeData[0]);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);

/**
 * This scenario is based on the bug described in this ticket
 * http://forge.prestashop.com/browse/BOOM-2533
 **/

scenario('Check that the TinyMCE field is well displayed in the Back Office', client => {
  test('should open the browser', () => client.open());
  test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  test('should go to "Products" page', () => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
  test('should click on "New Product" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));
  test('should check the appearance of the TinyMCE field', () => client.isExisting(AddProductPage.summary_tinymce_buttons, 2000));
}, 'common_client', true);

scenario('Edit the connected employee profile in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_scenarios.editEmployee(employeeData[1]);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);