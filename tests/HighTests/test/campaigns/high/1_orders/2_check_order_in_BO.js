const {OrderPage} = require('../../../selectors/BO/order_page');

scenario('Check order in BO', client => {
  test('should status be equal to Awaiting check payment', () => client.checkTextValue(OrderPage.order_check_status,'Awaiting check payment'));
  test('should update order status to Refunded ', () => client.updateStatus('Payment error'));
  test('should click on update order status button ', () => client.waitForExistAndClick(OrderPage.update_order_status_button));
  test('should status be equal to Refunded', () => client.checkTextValue(OrderPage.order_check_status,'Payment error'));
  test('should update order status to Awaiting bank wire payment ', () => client.updateStatus('Awaiting bank wire payment'));
  test('should click on update order status button ', () => client.waitForExistAndClick(OrderPage.update_order_status_button));
  test('should status be equal to Awaiting bank wire payment', () => client.checkTextValue(OrderPage.order_check_status,'Awaiting bank wire payment'));
  test('should update order status to Payment accepted ', () => client.updateStatus('Payment accepted'));
  test('should click on update order status button ', () => client.waitForExistAndClick(OrderPage.update_order_status_button));
  test('should status be equal to Payment accepted', () => client.checkTextValue(OrderPage.order_check_status,'Payment accepted'));
  test('should check the shipping cost', () => client.checkTextValue(OrderPage.check_shipping_Cost,'€8.40'));
  test('should check order message', () => client.checkTextValue(OrderPage.check_message_order,'Order message test'));
  test('should check the product information', () => client.checkProductInformation('White', 'Blouse', 'demo2', 'S'));
  test('should check quantity ', () => client.checkTextValue(OrderPage.check_quantity,'4'));
  test('should check basic price ', () => client.checkBasicPrice());
  test('should check customer name ', () => client.checkCustomer('John DOE'));
  test('should update order status to Payment accepted ', () => client.updateStatus('Delivered'));
  test('should click on update order status button ', () => client.waitForExistAndClick(OrderPage.update_order_status_button));
  test('should status be equal to Payment Delivered', () => client.checkTextValue(OrderPage.order_check_status,'Delivered'))
}, 'order/order');
