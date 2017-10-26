scenario('Catalog bulk action', () => {

  scenario('Log in back office and go to catalog list', client => {
    test('should open the browser', () => client.open());
    test('should sign in BO', () => client.signinBO());
    test('should go to catalog list', () => client.goToCatalog());
  }, 'catalogbulkaction');

  scenario('Deactivate the product list', client => {
    test('should select all product', () => client.selectAllProduct());
    test('should Disable the product list', () => client.disableProductlist());
    test('should check deactivated product ', () => client.checkProductListMsg('Produit(s) désactivé(s) avec succès.','clear'));
  }, 'catalogbulkaction');

  scenario('Activate the product list', client => {
    test('should select all product', () => client.selectAllProduct());
    test('should enable the product list', () => client.enableProductlist());
    test('should check activated product ', () => client.checkProductListMsg('Produit(s) activé(s) avec succès.', 'check'));
  }, 'catalogbulkaction');

}, 'catalogbulkaction', true);
