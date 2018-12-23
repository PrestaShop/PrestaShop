const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');

const common_scenarios = require('../../common_scenarios/product');

let productData = {
  name: 'DP',
  quantity: "50",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'a'
};

scenario('Delete product', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');

  common_scenarios.createProduct(AddProductPage, productData);

  scenario('Delete product "DP' + date_time + '"', client => {
    test('should go to "Product Settings" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should set the product name "DP' + date_time + '" in the search input', () => client.waitAndSetValue(CatalogPage.name_search_input, productData.name + date_time));
    test('should click on the "ENTER" key', () => client.keys('Enter'));
    test('should click on the "dropdown" icon', () => client.waitForExistAndClick(CatalogPage.dropdown_toggle));
    test('should click on the "delete" icon', () => client.waitForExistAndClick(CatalogPage.delete_button));
    test('should click on the "delete now" button', () => client.waitForVisibleAndClick(CatalogPage.delete_confirmation));
    test('should verify the appearance of the green validation message', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct successfully deleted.'));

    scenario('should check that the created product has been deleted', client => {
      test('should search for the created product', () => client.waitAndSetValue(CatalogPage.name_search_input, productData.name + date_time));
      test('should click on the "ENTER" key', () => client.keys('Enter'));
      test('should get a message indicates that no result found', () => client.checkTextValue(CatalogPage.search_result_message, 'There is no result for this search', "contain"));
      test('should click on "Reset" button', () => client.waitForVisibleAndClick(CatalogPage.reset_button));
    }, 'product/product');

  }, 'product/product');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'product/product');

  scenario('Login in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'product/product');

  scenario('check that the product "DP' + date_time + ' doesn\'t exist in the front office', client => {
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
    test('should check that the product "DP' + date_time + '" doesn\'t exist ', () => client.isNotExisting(SearchProductPage.product_result_name));
  }, 'product/product');

  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'product/product');

}, 'product/product', true);
