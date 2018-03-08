const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('./pages');

let categoryData = {
  name: 'Category',
  parent_category: '1',
  description: 'category description',
  meta_title: 'category meta title',
  meta_description: 'category meta description',
  meta_keywords: 'category meta keywords',
};

let newCategoryData = {
  name: 'editCategory',
  parent_category: '1',
  description: 'new category description',
  meta_title: 'new category meta title',
  meta_description: 'new category meta description',
  meta_keywords: 'new category meta keywords',
};

scenario('Create, edit, delete and delete with bulk actions page category', client => {

  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  common_scenarios.createCategory(categoryData);
  common_scenarios.checkCategoryBO(categoryData.name);
  common_scenarios.editCategory(categoryData.name, newCategoryData);
  common_scenarios.checkCategoryBO(newCategoryData.name);
  common_scenarios.deleteCategory(newCategoryData.name);
  common_scenarios.createCategory(categoryData);
  common_scenarios.createCategory(categoryData);
  common_scenarios.deleteCategoryWithBulkActions(categoryData.name);

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');

}, 'common_client', true);