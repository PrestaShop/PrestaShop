scenario('Check default product sort', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signinBO());
  test('should go to product menu', () => client.goToProductMenu());
  test('should check default product sort', () => client.getElementID())
}, 'product/product', true);
