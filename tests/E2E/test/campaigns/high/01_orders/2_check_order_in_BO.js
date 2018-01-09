const {OrderPage} = require('../../../selectors/BO/order_page');

scenario('Check order in BO', client => {
    test('should check status to be equal to "Awaiting check payment"', () => client.checkTextValue(OrderPage.order_status, 'Awaiting check payment'));
    test('should set order status to "Refunded"', () => client.updateStatus('Payment error'));
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    test('should check that the status is "Refunded"', () => client.checkTextValue(OrderPage.order_status, 'Payment error'));
    test('should set order status to "Awaiting bank wire payment"', () => client.updateStatus('Awaiting bank wire payment'));
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    test('should check that the status is "Awaiting bank wire payment"', () => client.checkTextValue(OrderPage.order_status, 'Awaiting bank wire payment'));
    test('should set order status to "Payment accepted"', () => client.updateStatus('Payment accepted'));
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    test('should check that the status is "Payment accepted"', () => client.checkTextValue(OrderPage.order_status, 'Payment accepted'));
    test('should check that the "shipping cost" is equal to €8.40', () => client.checkTextValue(OrderPage.shipping_cost, '€8.40'));
    test('should check that the "order message" is equal to "Order message test"', () => client.checkTextValue(OrderPage.message_order, 'Order message test'));
    test('should check "the product information"', () => client.checkProductInformation('White', 'Blouse', 'demo2', 'S'));
    test('should check that the "quantity" is  equal to "4"', () => client.checkTextValue(OrderPage.order_quantity, '4'));
    test('should check "basic price" ', () => client.checkBasicPrice());
    test('should check that the "customer" is equal to "John DOE"', () => client.checkTextValue(OrderPage.customer_name, 'John DOE', "contain"));
    test('should set order status to Payment accepted ', () => client.updateStatus('Delivered'));
    test('should click on "UPDATE STATUS" button', () => client.waitForExistAndClick(OrderPage.update_status_button));
    test('should check status to be equal to "Payment Delivered"', () => client.checkTextValue(OrderPage.order_status, 'Delivered'))
}, 'order/order');
