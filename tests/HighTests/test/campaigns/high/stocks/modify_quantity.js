scenario('Check modify quantity', () => {

  scenario('Log in back office and go to catalog stock', client => {
    test('should open the browser', () => client.open());
    test('should sign in Back Office', () => client.signinBO());
  }, 'quantity');

  scenario('Change group of product quantity', client => {
    test('should go to stock list', () => client.goToCatalogStock());
    test('should change the quantity of the first product', () => client.modifyFirstProductQuantity());
    test('should save the quantity of the product and check the success message', () => client.saveGroupProduct());
    test('should go to movements tabs', () => client.goToStockMovements());
    test('should check movements history', () => client.checkMovements("15","+"));
    test('should go to stock tabs', () => client.goToStock());
    test('should change the quantity of the second product', () => client.modifySecondProductQuantity());
    test('should save the quantity of the product and check the success message', () => client.saveGroupProduct());
    test('should go to movements tabs', () => client.goToStockMovements());
    test('should check movements history', () => client.checkMovements("50","+"));
  }, 'quantity');

  scenario('Change the quantity of single product ', client => {
    test('should go to stock tabs', () => client.goToStock());
    test('should get the quantity of the third product', () => client.getThirdProductQuantity());
    test('should change the quantity of the third product', () => client.modifyThirdProductQuantity());
    test('should save the quantity of the third product', () => client.saveThirdProduct());
    test('should go to movements tabs', () => client.goToStockMovements());
    test('should check movements history', () => client.checkMovements("3","+"));
    test('should go to stock tabs', () => client.goToStock());
    test('should get the quantity of the fourth product', () => client.getFourthProductQuantity());
    test('should change the quantity of the fourth product', () => client.modifyFourthProductQuantity());
    test('should save the quantity of the fourth product', () => client.saveFourthProduct());
    test('should go to movements tabs', () => client.goToStockMovements());
    test('should check movements history', () => client.checkMovements("50","-"));
  }, 'quantity');

}, 'quantity',true);

