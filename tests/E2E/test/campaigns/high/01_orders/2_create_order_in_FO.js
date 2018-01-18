const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {OrderPage} = require('../../../selectors/BO/order');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const common_scenarios = require('./order');
let promise = Promise.resolve();

scenario('Create order in the Front Office', () => {
  scenario('Open the browser and connect to the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'order');
  scenario('Create order in the Front Office', () => {
    common_scenarios.createOrder();
  }, 'order');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'order');
}, 'order', true);

scenario('Check the created order in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');

  scenario('Check the created order in the Back Office', client => {
    test('should go to "Orders" page', () => client.goToSubtabMenuPage(OrderPage.orders_subtab, OrderPage.order_submenu));
    test('should search the order created by reference', () => client.waitAndSetValue(OrderPage.search_by_reference_input, global.tab['reference']));
    test('should go to search order', () => client.waitForExistAndClick(OrderPage.search_order_button));
    test('should go to the order ', () => client.scrollWaitForExistAndClick(OrderPage.view_order_button.replace('%NUMBER', 1)));
    test('should check the customer name ', () => client.checkTextValue(OrderPage.customer_name, 'John DOE', 'contain'));
    test('should status be equal to Awaiting bank wire payment ', () => client.checkTextValue(OrderPage.order_status, 'Awaiting bank wire payment'));
    test('should check the shipping price', () => client.checkTextValue(OrderPage.shipping_cost, global.tab['shipping_price']));
    test('should check the product', () => client.checkTextValue(OrderPage.product_name, global.tab['product']));
    test('should check the order message ', () => client.checkTextValue(OrderPage.message_order, 'Order message test'));
    test('should check the total price', () => client.checkTextValue(OrderPage.total_price, global.tab["total_price"]));
    test('should check basic product price', () => {
      return promise
        .then(() => client.scrollTo(OrderPage.edit_product_button))
        .then(() => client.waitForExistAndClick(OrderPage.edit_product_button))
        .then(() => client.checkAttributeValue(OrderPage.product_basic_price, 'value', global.tab["basic_price"].replace('€', '')))
    });
    test('should check shipping method ', () => client.checkTextValue(OrderPage.shipping_method, global.tab["method"].split('\n')[0], 'contain'));
  }, "order");
}, 'order', true);
