scenario('Create order in BO', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to orders list', () => client.goToOrdersList());
  test('should create new order', () => client.clickOnAddNewOrderButton());
  test('should search for a customer', () => client.searchCustomer('john doe'));
  test('should search for a product by name', () => client.searchProduct('Blouse'));
  test('should select the product type', () => client.selectProductType('Blouse'));
  test('should select the product combination', () => client.selectProductCombination('S - White - €26.99'));
  test('should select the product quantity', () => client.addProductQuantity('4'));
  test('should click on add to cart button', () => client.clickOnAddToCartButton());
  test('should get the basic product price', () => client.getBasicPriceValue());
  test('should select delivery option ', () => client.selectDelivery());
  test('should add an order message ', () => client.addOrderMessage());
  test('should select a payment type ', () => client.selectPayment());
  test('should select an order status ', () => client.selectOrderStatus());
  test('should create the order', () => client.clickOnCreateOrder());
}, "order_BO");
