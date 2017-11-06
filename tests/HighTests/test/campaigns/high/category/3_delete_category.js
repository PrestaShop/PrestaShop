scenario('Delete category', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to category', () => client.goToCategoryList());
  test('should search for the category', () => client.searchCategoryBO());
  test('should delete category', () => client.deleteCategory());
  test('should delete category success panel', () => client.successPanel('Successful deletion.'));
  test('should sign out BO', () => client.signoutBO());
}, 'category', true);

scenario('Create category', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to category', () => client.goToCategoryList());
  test('should create new category', () => client.createCategory());
  test('should add category name', () => client.addCategoryName());
  test('should add category image', () => client.addCategoryImage());
  test('should add category thumb', () => client.addCategoryThumb());
  test('should add category title', () => client.addCategoryTitle());
  test('should add category meta desciption', () => client.addCategoryMetaDescription());
  test('should add category meta keys words', () => client.addCategoryMetakeyswords());
  test('should add category simplify url', () => client.addCategorySimplifyUrl());
  test('should add category save', () => client.addCategorySave());
  test('should check category success panel', () => client.successPanel('Successful creation.'));
  test('should sign out BO', () => client.signoutBO());
}, 'category', true);

scenario('Delete category with action group', client => {
  test('should open the browser', () => client.open());
  test('should sign in BO', () => client.signinBO());
  test('should go to category', () => client.goToCategoryList());
  test('should search for the category', () => client.searchCategoryBO());
  test('should update category', () => client.deleteCategoryWithActiongroup());
  test('should check delete category success panel', () => client.successPanel('The selection has been successfully deleted.'));
  test('should sign out BO', () => client.signoutBO());
}, 'category', true);
