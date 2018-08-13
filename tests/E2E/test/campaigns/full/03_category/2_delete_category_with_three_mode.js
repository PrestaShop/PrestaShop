const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonScenarios = require('../../common_scenarios/category');
const commonProductScenarios = require('../../common_scenarios/product');

let productData = [{
  name: 'P1',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'test_1',
  categories: {
    1: {
      name: 'category_name',
      main_category: false
    }
  },
}, {
  name: 'P2',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'test_2',
  categories: {
    1: {
      name: 'category_name',
      main_category: true
    },
    2: {
      name: 'home',
      main_category: false
    }
  },
}, {
  name: 'P3',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'test_3',
  categories: {
    1: {
      name: 'category_name',
      main_category: false
    },
    2: {
      name: 'home',
      main_category: true
    }
  },
}];

let categoryData = {
    name: 'category_name',
    description: 'description of category',
    picture: 'category_image.png',
    thumb_picture: 'category_miniature.png',
    meta_title: 'meta title category',
    meta_description: 'meta description category',
    meta_keywords: {
      1: 'first key',
      2: 'second key'
    },
    friendly_url: 'prestashop_Friendly_url'
  };

/**
 * This scenario is based on the bug described in this ticket
 * http://forge.prestashop.com/browse/BOOM-4293
 **/

scenario('Create, edit, check and delete "Category" with the "linkanddisable" mode', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'category');

  commonScenarios.createCategory(categoryData);
  commonScenarios.checkCategoryBO(categoryData);
  commonScenarios.configureMainMenu();
  for (let i = 0; i < 3; i++) {
    commonProductScenarios.createProduct(AddProductPage, productData[i]);
  }
  commonScenarios.editParentCategory(categoryData, 'Accessories');
  commonScenarios.checkCategoryFO(categoryData, 1, true);
  commonScenarios.deleteCategoryWithDeleteMode(categoryData, 'Accessories');
  commonScenarios.checkDeleteModeBO(productData, 'linkanddisable');
  commonScenarios.checkDeleteModeFO(productData, 'linkanddisable', false);
  for (let j = 0; j < 3; j++) {
    commonProductScenarios.deleteProduct(AddProductPage, productData[j]);
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'category');
}, 'category', true);

scenario('Create, edit, check and delete "Category" with the "link" mode', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'category');
  commonScenarios.createCategory(categoryData);
  commonScenarios.editParentCategory(categoryData, 'Accessories');
  for (let i = 0; i < 3; i++) {
    commonProductScenarios.createProduct(AddProductPage, productData[i]);
  }
  commonScenarios.deleteCategoryWithDeleteMode(categoryData, 'Accessories', 'link');
  commonScenarios.checkDeleteModeBO(productData, 'link');
  commonScenarios.checkDeleteModeFO(productData);
  for (let j = 0; j < 3; j++) {
    commonProductScenarios.deleteProduct(AddProductPage, productData[j]);
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'category');
}, 'category', true);

scenario('Create, edit, check and delete "Category" with the "delete" mode', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'category');
  commonScenarios.createCategory(categoryData);
  commonScenarios.editParentCategory(categoryData, 'Accessories');
  for (let i = 0; i < 3; i++) {
    commonProductScenarios.createProduct(AddProductPage, productData[i]);
  }
  commonScenarios.deleteCategoryWithDeleteMode(categoryData, 'Accessories', 'delete');
  commonScenarios.checkDeleteModeBO(productData[0], 'delete', true);
  commonScenarios.checkDeleteModeFO(productData, 'delete');
  for (let j = 1; j < 3; j++) {
    commonProductScenarios.deleteProduct(AddProductPage, productData[j]);
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'category');
}, 'category', true);
