scenario('Check order movement', client => {
  test('should open the browser', () => client.open());
  test('should sign in the Back Office', () => client.signinBO());
  test('should go to catalog stocks', () => client.goToCatalogStock());
  test('should go to movements tabs', () => client.goToStockMovements());
  test('should check movement history', () => client.checkMovement(1,global.orderQuantity, "-", "Customer Order"));
}, 'stocks');
