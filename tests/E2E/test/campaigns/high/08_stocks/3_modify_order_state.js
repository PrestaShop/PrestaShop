const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OrderPage} = require('../../../selectors/BO/order_page');

scenario('Change order state to "Delivred"', client => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');
  scenario('Change order state to "Delivred"', client => {
    test('should click on "Orders" menu', () => client.waitForExistAndClick(OrderPage.orders_subtab));
    test('should go to the first order', () => client.waitForExistAndClick(OrderPage.view_order_button.replace('%NUMBER', 1)));
    test('should change order state to "Delivered"', () => client.changeOrderState(OrderPage, 'Delivered'));
    test('should get the order quantity', () => client.getTextInVar(OrderPage.order_quantity,"orderQuantity"));
  }, 'stocks');
}, 'stocks', true);
