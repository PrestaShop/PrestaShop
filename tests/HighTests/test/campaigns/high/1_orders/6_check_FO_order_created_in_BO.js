scenario('Check order created in BO', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to orders list', () => client.goToOrdersList());
  test('should search the order created by reference', () => client.searchOrderCreatedByReference());
  test('should go to order ', () => client.viewOrder());
  test('should check the customer name ', () => client.checkCustomerName());
  test('should status be equal to Awaiting bank wire payment ', () => client.checkOrderStatus('Awaiting bank wire payment'));
  test('should check the shipping price', () => client.checkShippingPrice());
  test('should check the product', () => client.checkProduct());
  test('should check the order message ', () => client.checkOrderMessage('Order message test'));
  test('should check the total price', () => client.checkTotalPrice());
  test('should check basic product price', () => client.checkBasicPrice());
  test('should check product quantity ', () => client.checkQuantity('4'));
  test('should check shipping method ', () => client.checkShippingMethod());
}, "check_order_BO",true);
