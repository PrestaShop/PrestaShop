scenario('Update feature', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to the feature list', () => client.goToFeatureList());
  test('should search for the created feature', () => client.searchFeature());
  test('should update feature name', () => client.updateFeatureName());
  test('should check updated feature success panel', () => client.successPanel('Successful update.'));
  test('should update feature value name', () => client.updateFeatureValueName());
  test('should check the updated feature value success panel', () => client.successPanel('Successful update.'));
  test('should sign out BO', () => client.signoutBO());
}, 'feature', true);

scenario('Check the updated feature in Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in FO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct('Feature'));
  test('should check that the feature has been updated in the Front Office', () => client.checkUpdatedFeature());
  test('should sign in FO', () => client.signoutFO());
}, 'feature', true);
