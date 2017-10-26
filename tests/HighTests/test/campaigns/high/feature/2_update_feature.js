scenario('Update feature', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to the feature list', () => client.goToFeatureList());
  test('should search for the created feature', () => client.searchFeature());
  test('should update feature name', () => client.updateFeaturename());
  test('should check updated feature success panel', () => client.SuccessPanel('Successful update.'));
  test('should update feature value name', () => client.updateFeatureValuename());
  test('should check updated value success panel', () => client.SuccessPanel('Successful update.'));
  test('should sign out BO', () => client.signoutBO());
}, 'feature', true);

scenario('The Check of the Updated feature in Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in FO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct());
  test('should Check updated feature in Front Office', () => client.checkUpdatedFeature());
  test('should sign in FO', () => client.signoutFO());
}, 'feature', true);
