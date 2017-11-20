scenario('Update category', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to category', () => client.goToCategoryList());
  test('should search for the category', () => client.searchCategoryBO());
  test('should update category', () => client.updateCategory());
  test('should check category success panel', () => client.successPanel('Successful update.'));
  test('should sign out BO', () => client.signoutBO());
}, 'category', true);

scenario('Check category in BO', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to category ', () => client.goToCategoryBO());
  test('should search for category ', () => client.searchCategoryBO());
  test('should check category image', () => client.checkCategoryImage());
  test('should check category image thumb', () => client.checkCategoryImageThumb());
  test('should check category title', () => client.checkCategoryTitle());
  test('should check category meta description', () => client.checkCategoryMetaDescription());
  test('should check category simplify url', () => client.checkCategorySimplifyURL());
  test('should sign out BO', () => client.signoutBO());
}, 'category', true);


scenario('Check category in FO', client => {
  test('should open the browser', () => client.open());
  test('should sign in FO', () => client.signinFO());
  test('should change front office language to english', () => client.languageChange('english'));
  test('should open product list', () => client.openProductList());
  test('should check category existence', () => client.checkCategoryExistenceFO());
  test('should sign out FO', () => client.signoutFO());
}, 'category', true);
