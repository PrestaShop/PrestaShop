scenario('Modify quantity and check movement for single product', client => {
  test('should open the browser', () => client.open());
  test('should sign in the Back Office', () => client.signinBO());
  test('should go to catalog stocks', () => client.goToCatalogStock());
  test('should get quantity of the third product', () => client.getThirdProductQuantity());
  test('should change third product quantity', () => client.modifyThirdProductQuantity());
  test('should save third product quantity', () => client.saveThirdProduct());
  test('should go to movements tab', () => client.goToStockMovements());
  test('should check movement history', () => client.checkMovement(1, "3", "+", "Employee Edition"));
  test('should go to stock tab', () => client.goToStock());
  test('should get fourth product quantity', () => client.getFourthProductQuantity());
  test('should change fourth product quantity', () => client.modifyFourthProductQuantity());
  test('should save fourth product quantity', () => client.saveFourthProduct());
  test('should go to movements tab', () => client.goToStockMovements());
  test('should check movement history', () => client.checkMovement(1, "3", "-", "Employee Edition"));
}, 'stocks', true);
