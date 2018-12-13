const {AccessPageBO} = require('../../../selectors/BO/access_page');
const commonScenarios = require('../../common_scenarios/category');
const welcomeScenarios = require('../../common_scenarios/welcome');

let categoryData = {
    name: 'category',
    description: 'description of category',
    picture: 'category_image.png',
    thumb_picture: 'category_miniature.png',
    meta_title: 'test category',
    meta_description: 'this is the meta description',
    meta_keywords: {
      1: 'keyswords'
    },
    friendly_url: 'category'
  },
  editedCategoryData = {
    name: 'category_update',
    description: 'Description of category update',
    meta_title: 'test title category update',
    meta_description: 'meta description category update',
    meta_keywords: {
      1: 'first key',
      2: 'updated Key'
    },
    friendly_url: 'Updated_Friendly_url',
  };

scenario('Create, edit, delete and check "Category"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'category');
  welcomeScenarios.findAndCloseWelcomeModal();
  commonScenarios.createCategory(categoryData);
  commonScenarios.checkCategoryBO(categoryData);
  commonScenarios.configureMainMenu();
  commonScenarios.checkCategoryFO(categoryData, 1);
  commonScenarios.editCategory(categoryData, editedCategoryData);
  commonScenarios.checkCategoryBO(editedCategoryData);
  commonScenarios.checkCategoryFO(editedCategoryData, 2);
  /* Delete category using the delete mode */
  commonScenarios.deleteCategoryWithDeleteMode(editedCategoryData);
  /* Delete category with bulk action */
  commonScenarios.createCategory(categoryData);
  commonScenarios.deleteCategoryWithBulkAction(categoryData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'category');
}, 'category', true);

