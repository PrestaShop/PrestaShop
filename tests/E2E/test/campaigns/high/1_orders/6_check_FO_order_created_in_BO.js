const {OrderPage} = require('../../../selectors/BO/order_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');

scenario('Check order created in BO', client => {
  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in Back Office', () => client.signInBO(AccessPageBO));
  }, 'order/order');
  scenario('Check order created in BO', client => {
    test('should go to "Orders" page', () => client.goToSubtabMenuPage(OrderPage.orders_subtab, OrderPage.order_submenu));
    test('should search the order created by reference', () => client.waitAndSetValue(OrderPage.reference_search, global.tab['reference']));
    test('should go to search order', () => client.waitForExistAndClick(OrderPage.search_order_button));
    test('should go to the order ', () => client.waitForExistAndClick(OrderPage.view_order_button));
    test('should check the customer name ', () => client.checkTextValue(OrderPage.customer_name, 'John DOE', 'contain'));
    test('should status be equal to Awaiting bank wire payment ', () => client.checkTextValue(OrderPage.order_check_status, 'Awaiting bank wire payment', "equal"));
    test('should check the shipping price', () => client.checkTextValue(OrderPage.check_shipping_price, global.tab['shipping_price'], "equal"));
    test('should check the product', () => client.checkTextValue(OrderPage.check_product, global.tab['product'], "equal"));
    test('should check the order message ', () => client.checkTextValue(OrderPage.check_message_order, 'Order message test', "equal"));
    test('should check the total price', () => client.checkTextValue(OrderPage.check_total_price, global.tab["total_price"], "equal"));
    test('should check basic product price', () => client.checkBasicToTalPrice());
    test('should check shipping method ', () => client.checkShippingMethod());
  }, "order/order");
}, "order/order", true);
