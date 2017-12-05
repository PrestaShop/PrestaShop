const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');

scenario('Delete category', client => {
  test('should open the browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to category', () => client.goToCategoryList());
  test('should search for the category', () => client.searchCategoryBO());
  test('should delete category', () => client.deleteCategory());
  test('should delete category success panel', () => client.successPanel('Successful deletion.'));
}, 'category', true);

scenario('Create category', client => {
  test('should open the browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to category', () => client.goToCategoryList());
  test('should create new category', () => client.createCategory());
  test('should add category name', () => client.addCategoryName());
  test('should add category image', () => client.addCategoryImage());
  test('should add category thumb', () => client.addCategoryThumb());
  test('should add category title', () => client.addCategoryTitle());
  test('should add category meta desciption', () => client.addCategoryMetaDescription());
  test('should add category meta keys words', () => client.addCategoryMetakeyswords());
  test('should add category simplify url', () => client.addCategorySimplifyUrl());
  test('should add category save', () => client.SaveCategory());
  test('should check category success panel', () => client.successPanel('Successful creation.'));
}, 'category', true);

scenario('Delete category with action group', client => {
  test('should open the browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to category', () => client.goToCategoryList());
  test('should search for the category', () => client.searchCategoryBO());
  test('should delete category with action group', () => client.deleteCategoryWithActionGroup());
  test('should check delete category success panel', () => client.successPanel('The selection has been successfully deleted.'));
}, 'category', true);
