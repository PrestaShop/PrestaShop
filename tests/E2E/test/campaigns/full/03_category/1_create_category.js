const {AccessPageBO} = require('../../../selectors/BO/access_page');
let commonScenarios = require('../../common_scenarios/category');

let categoryData = {
  name: 'NC',
  description: 'Description of NC category',
  picture: 'category_image.png',
  thumb_picture: 'category_miniature.png',
  thumb_menu_picture: 'category_miniature_menu.jpeg',
  meta_title: 'meta title category',
  meta_description: 'meta description category',
  meta_keywords: {
    1: 'NC',
    2: 'Category'
  },
  friendly_url: 'NC',
}, editedCategoryData = {
  name: 'NC_update',
  description: 'Description of NC category update',
  picture: 'category_image.png',
  thumb_picture: 'category_miniature.png',
  thumb_menu_picture: 'category_miniature_menu.jpeg',
  meta_title: 'meta title category update',
  meta_description: 'meta description category update',
  meta_keywords: {
    1: 'NC',
    2: 'Category',
    3: 'Updated'
  },
  friendly_url: 'NC',
};

scenario('Create "Category"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'category');
  commonScenarios.createCategory(categoryData);
  commonScenarios.checkCategoryBO(categoryData);
  commonScenarios.checkCategoryFO(categoryData, 1);
  commonScenarios.editCategory(categoryData, editedCategoryData);
  commonScenarios.checkCategoryBO(editedCategoryData);
  commonScenarios.checkCategoryFO(editedCategoryData, 2);
  commonScenarios.deleteCategoryWithDeleteMode(editedCategoryData);
  commonScenarios.createCategory(categoryData);
  commonScenarios.deleteCategoryWithDeleteMode(categoryData, 'link');
  commonScenarios.createCategory(categoryData);
  commonScenarios.deleteCategoryWithDeleteMode(categoryData, 'delete');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'category');
}, 'category', true);