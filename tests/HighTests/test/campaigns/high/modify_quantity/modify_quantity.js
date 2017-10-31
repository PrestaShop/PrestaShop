scenario('Check modify quantity', () => {

  scenario('Log in back office and go to catalog stock', client => {
    test('should open the browser', () => client.open());
    test('should sign in BO', () => client.signinBO());
    test('should go to stock list', () => client.goToCatalogStock());
  }, 'quantity');

  scenario('Change the quantity of group of product', client => {
    test('should modify first product quantity', () => client.modifyFirstProductQuantity());
    test('should modify third product quantity', () => client.modifySecondProductQuantity());
    test('should save the quantity of the product ', () => client.save());
  }, 'quantity');

  scenario('Change the quantity of single product ', client => {
    test('should modify first product quantity', () => client.modifyThirdProductQuantity());

    test('should save the quantity of the product ', () => client.save());
  }, 'quantity');

}, 'quantity',true);

