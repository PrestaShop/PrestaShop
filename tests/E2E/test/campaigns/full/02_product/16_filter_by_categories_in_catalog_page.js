/**
 * This script is based on the scenario described in this test link
 * [id="PS-105"][Name="Filter by categories in catalog page"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const commonProduct = require('../../common_scenarios/product');

scenario('Filters by categories in catalog page', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Check the filtering operation of the categories in the product page', client => {
    test('should go to "Catalog > Categories" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu));
    test('should get categories number', () => client.getCategoriesPageNumber('table-category'));
    test('should get all categories and sub categories', () => commonProduct.getCategories(client));
    test('should check the existence of categories in "Filter by categories" list in catalog page', () => commonProduct.checkCategories(client));
    test('should filter by category "Accessories" in catalog page then check results', () => commonProduct.checkFiltersCategories(client));
  }, 'product/product');
}, 'common_client', true);
