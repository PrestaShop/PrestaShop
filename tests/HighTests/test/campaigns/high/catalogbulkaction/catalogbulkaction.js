scenario('Catalog bulk action', () => {

  scenario('Log in back office and go to catalog list', client => {
    test('should open the browser', () => client.open());
    test('should sign in BO', () => client.signinBO());
    test('should go to catalog list', () => client.goToCatalog());
  }, 'catalogbulkaction');

  scenario('Disable the product list', client => {
    test('should select all product', () => client.selectAllProduct());
    test('should disable all products', () => client.disableProductlist());
    test('should check that all products have been disabled ', () => client.checkProductListMsg('Product(s) successfully deactivated.', 'clear'));
  }, 'catalogbulkaction');

  scenario('Enable the product list', client => {
    test('should select all product', () => client.selectAllProduct());
    test('should enable the product list', () => client.enableProductlist());
    test('should check that all products have been enabled ', () => client.checkProductListMsg('Product(s) successfully activated.', 'check'));
  }, 'catalogbulkaction');

  scenario('Duplicate the product list', client => {
    test('should select all product', () => client.selectAllProduct());
    test('should duplicate the product list', () => client.duplicateProductlist());
    test('should check that all products have been duplicated ', () => client.checkProductListMsg('Product(s) successfully duplicated.', 'clear'));
  }, 'catalogbulkaction');

  scenario('Enable the product list', client => {
    test('should select all product', () => client.selectAllProduct());
    test('should enable the product list', () => client.enableProductlist());
    test('should check that all products have been enabled', () => client.checkProductListMsg('Product(s) successfully activated.', 'check'));
  }, 'catalogbulkaction');

}, 'catalogbulkaction', true);
