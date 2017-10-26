scenario('Delete feature', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to the feature list', () => client.goToFeatureList());
  test('should search for the created feature', () => client.searchFeature());
  test('should delete feature', () => client.deleteFeature());
  test('should check feature value update success panel', () => client.SuccessPanel('Successful deletion.'));
  test('should sign out BO', () => client.signoutBO());
}, 'feature', true);

scenario('The Check the deleted feature in Front Office', client => {
  test('should open the browser', () => client.open());
  test('should sign in FO', () => client.signinFO());
  test('should search for the product', () => client.searchForProduct());
  test('should Check deleted feature in Front Office', () => client.checkDeletedFeature());
  test('should sign in FO', () => client.signoutFO());
}, 'feature', true);
