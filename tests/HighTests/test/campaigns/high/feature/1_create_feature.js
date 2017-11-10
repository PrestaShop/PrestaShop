scenario('Create feature', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to the feature list', () => client.goToFeatureList());
  test('should add new feature', () => client.addNewFeature());
  test('should add new feature name', () => client.addNewFeatureName());
  test('should save feature ', () => client.saveNewFeature());
  test('should check feature success panel', () => client.successPanel('Successful creation.'));
  test('should search for the created feature', () => client.searchFeature());
  test('should select the created feature', () => client.selectFeature());
  test('should add value to the created feature', () => client.addValueToFeature());
  test('should check feature value success panel', () => client.successPanel('Successful creation.'));
  test('should sign out BO', () => client.signoutBO());
}, 'feature', true);

scenario('Create product and add the new feature', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go product menu', () => client.goToProductMenu());
  test('should add new product', () => client.addNewProduct());
  test('should add product name', () => client.addProductName('Feature'));
  test('should add product quantity', () => client.addProductQuantity());
  test('should add product price', () => client.addProductPrice());
  test('should add product feature', () => client.addProductTypeFeature());
  test('should publish the product en ligne', () => client.productEnligne());
  test('should save the product', () => client.saveProduct());
  test('should close the green validation', () => client.closeGreenValidation());
  test('should sign out BO', () => client.signoutBO());
}, 'create_product', true);

scenario('Check the created feature in Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in FO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct('Feature'));
  test('should check the created feature in the Front Office', () => client.checkCreatedFeature());
  test('should sign in FO', () => client.signoutFO());
}, 'feature', true);
