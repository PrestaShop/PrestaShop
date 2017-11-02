scenario('Modify quantity and check the movement of a group of product', client => {
  test('should open the browser', () => client.open());
  test('should sign in the Back Office', () => client.signinBO());
  test('should go to catalog stocks', () => client.goToCatalogStock());
  test('should modify quantity of the first product', () => client.modifyFirstProductQuantity());
  test('should modify quantity of the second product', () => client.modifySecondProductQuantity());
  test('should save quantity with bulk action and check the success message', () => client.saveGroupProduct());
  test('should go to movements tab', () => client.goToStockMovements());
  test('should check first product movement', () => client.checkMovement(1, "15", "+", "Employee Edition"));
  test('should check second product movement', () => client.checkMovement(2, "50", "+", "Employee Edition"));
}, 'stocks', true);
