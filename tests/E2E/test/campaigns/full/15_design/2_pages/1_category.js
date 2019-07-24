/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-120"][Name="Create a category"]
 * [id="PS-121"][Name="Edit a category"]
 * [id="PS-122"][Name="Delete a category"]
 **/

const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {Menu} = require('../../../../selectors/BO/menu.js');
const common_scenarios = require('../../../common_scenarios/pages');
const welcomeScenarios = require('../../../common_scenarios/welcome');

let categoryDataWithoutSubCategory = {
  name: 'Category',
  parent_category: '1',
  description: 'category description',
  meta_title: 'category meta title',
  meta_description: 'category meta description',
  meta_keywords: 'category meta keywords'
};

let categoryDataWithSubCategory = {
  name: 'Category',
  parent_category: '1',
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

let pageData = {
  page_category: 'editCategory',
  meta_title: 'page1',
  meta_description: 'page meta description',
  meta_keyword: ["keyword", "page"],
  page_content: 'page content'
};

scenario('Create, edit and delete "CATEGORIES"', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'design');

  welcomeScenarios.findAndCloseWelcomeModal();
  common_scenarios.createCategory(categoryDataWithSubCategory);
  common_scenarios.checkCategoryBO(categoryDataWithSubCategory);
  common_scenarios.editCategory(categoryDataWithSubCategory, newCategoryData);

  scenario('go to "Design > Pages" page', client => {
    test('should go to "Design > Pages" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu));
  }, 'design');

  common_scenarios.createAndPreviewPage(pageData);
  common_scenarios.deleteCategory(newCategoryData.name);
  common_scenarios.deleteCategory(newCategoryData.sub_category.name);
  common_scenarios.createCategory(categoryDataWithoutSubCategory);
  common_scenarios.createCategory(categoryDataWithoutSubCategory);
  common_scenarios.categoryBulkActions(categoryDataWithSubCategory.name, "disable");
  common_scenarios.categoryBulkActions(categoryDataWithSubCategory.name, "enable");
  common_scenarios.categoryBulkActions(categoryDataWithSubCategory.name);

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'design');

}, 'design', true);
