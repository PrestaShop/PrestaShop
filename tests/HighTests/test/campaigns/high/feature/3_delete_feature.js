scenario('Delete feature', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to the feature list', () => client.goToFeatureList());
  test('should search for the created feature', () => client.searchFeature());
  test('should delete feature', () => client.deleteFeature());
  test('should check feature value update success panel', () => client.successPanel('Successful deletion.'));
  test('should sign out BO', () => client.signoutBO());
}, 'feature', true);

scenario('Check that the feature does not exist in the Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in FO', () => client.signinFO());
  test('should change front office language to english', () => client.languageChange('english'));
  test('should search for the product', () => client.searchForProduct());
  test('should check that the feature has been deleted in the Front Office', () => client.checkDeletedFeature());
  test('should sign in FO', () => client.signoutFO());
}, 'feature', true);
