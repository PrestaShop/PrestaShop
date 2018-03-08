const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('./pages');

let pageCategoryData = {
  name: 'Category',
  parent_category: '1',
  description: 'category description',
  meta_title: 'category meta title',
  meta_description: 'category meta description',
  meta_keywords: 'category meta keywords',
};

let newPageCategoryData = {
  name: 'editCategory',
  parent_category: '1',
  description: 'edited category description',
  meta_title: 'edited category meta title',
  meta_description: 'edited category meta description',
  meta_keywords: 'edited category meta keywords',
};

scenario('Create, edit, delete and delete with bulk actions page category', client => {

  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'design');

  common_scenarios.createPageCategory(pageCategoryData);
  common_scenarios.checkPageCategoryBO(pageCategoryData.name);
  common_scenarios.editPageCategory(pageCategoryData.name, newPageCategoryData);
  common_scenarios.checkPageCategoryBO(newPageCategoryData.name);
  common_scenarios.deletePageCategory(newPageCategoryData.name);

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'design');

}, 'design', true);