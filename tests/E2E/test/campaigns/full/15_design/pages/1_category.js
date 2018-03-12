const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('./pages');

let categoryDataWithoutSubCategory = {
  name: 'Category',
  parent_category: 1,
  description: 'category description',
  meta_title: 'category meta title',
  meta_description: 'category meta description',
  meta_keywords: 'category meta keywords',
};

let categoryData = {
  name: 'Category',
  parent_category: 1,
  description: 'category description',
  meta_title: 'category meta title',
  meta_description: 'category meta description',
  meta_keywords: 'category meta keywords',
  sub_category: {
    name: 'subCategory',
    description: 'sub category description',
    meta_title: 'sub category meta title',
    meta_description: 'sub category meta description',
    meta_keywords: 'sub category meta keywords'
  }
};

let newCategoryData = {
  name: 'editCategory',
  parent_category: '1',
  description: 'new category description',
  meta_title: 'new category meta title',
  meta_description: 'new category meta description',
  meta_keywords: 'new category meta keywords',
  sub_category: {
    name: 'subCategory',
    parent_category: '1',
    description: 'new sub category description',
    meta_title: 'new sub category meta title',
    meta_description: 'new sub category meta description',
    meta_keywords: 'new sub category meta keywords'
  }
};

scenario('Create, edit, delete and delete with bulk actions page category', client => {

  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'design');

  common_scenarios.createCategory(categoryData);
  common_scenarios.checkCategoryBO(categoryData);
  common_scenarios.editCategory(categoryData, newCategoryData);
  common_scenarios.deleteCategory(newCategoryData.name);
  common_scenarios.deleteCategory(newCategoryData.sub_category.name);
  common_scenarios.createCategory(categoryDataWithoutSubCategory);
  common_scenarios.createCategory(categoryDataWithoutSubCategory);
  common_scenarios.deleteCategoryWithBulkActions(categoryData.name);

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'design');

}, 'design');