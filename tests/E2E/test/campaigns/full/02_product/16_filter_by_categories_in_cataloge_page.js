const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const commonProduct = require('../../common_scenarios/product');
let promise = Promise.resolve();

scenario('Filters by categories in catalog page', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Check the filtering operation of the categories in the product page', client => {
    test('should go to categories page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
    test('should get the categories', () => {
      return promise
        .then(() => client.getCategoriesPageNumber('table-category'))
        .then(() => {
          if (global.categoriesPageNumber !== 0) {
            commonProduct.getCategories(global.categoriesPageNumber);
          }
          commonProduct.checkCategories(global.categoriesPageNumber);
        });
    }, 'category');
  }, 'category');
}, 'common_client');
