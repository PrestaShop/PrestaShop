const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {accountPage} = require('../../../selectors/FO/add_account_page');
const {ProductSettings} = require('../../../selectors/BO/shopParameters/product_settings');
const commonOrder = require('../../common_scenarios/order');
const commonProduct = require('../../common_scenarios/product');
let promise = Promise.resolve();

const customer_common_scenarios = require('../../common_scenarios/customer');

let customerData = {
  first_name: 'John',
  last_name: 'DOE',
  email_address: 'test@prestashop.com',
  password: '123456789',
  birthday: {
    day: '18',
    month: '12',
    year: '1991'
  }
};

scenario('Create Order with account', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'customer');

  customer_common_scenarios.createCustomer(customerData);

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'customer');

  scenario('Create order in the Front Office', () => {
    scenario('Open the browser and connect to the Front Office', client => {
      test('should login successfully in the Front Office', () => client.linkAccess(global.URL));
    }, 'order');

    commonOrder.createOrderFO('connect', date_time + customerData.email_address, customerData.password);

  }, 'order');
}, 'order', true);

scenario('Check the created order in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');

  commonOrder.checkOrderInBO();

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'order');
}, 'order', true);
