const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {ProductSettings} = require('../../../selectors/BO/shopParameters/product_settings');
const {productPage} = require('../../../selectors/FO/product_page');
const {Menu} = require('../../../selectors/BO/menu.js');

const commonScenarios = require('../../common_scenarios/product');

let promise = Promise.resolve();

global.productInfo = [];

scenario('Display all product', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');

  scenario('Check the product pagination in the Back Office', client => {
    test('should go to "Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should select "active product"', () => client.waitAndSelectByValue(ProductList.status_filter, "1"));
    test('should get the product number', () => {
      return promise
        .then(() => client.isVisible(ProductList.pagination_products))
        .then(() => client.getProductsNumber(ProductList.pagination_products))
        .then(() => {
          if (global.ps_mode_dev) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar);
          }
        });
    });

    test('should set "100" to items per page ', () => {
      return promise
        .then(() => client.isVisible(ProductList.item_per_page_select))
        .then(() => {
          if (global.isVisible) {
            client.waitAndSelectByValue(ProductList.item_per_page_select, '100');
          }
        });
    });

    test('should get all product\'s name', () => {
      for (let i = 1; i <= global.productsNumber; i++) {
        promise = client.getProductName(ProductList.product_name.replace('%ID', i));
        promise = client.pause(2000);
      }
      return promise
        .then(() => client.pause(2000));
    });

    test('should close "catalog" menu', () => client.waitForVisibleAndClick(Menu.Sell.Catalog.catalog_menu));
    test('should go to "Shop Parameters - Product Settings" page', () => {
      return promise
        .then(() => client.pause(3000))
        .then(() => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.product_settings_submenu));
    });
    test('should get the pagination Products per page value and check the created product in the Front Office', () => {
      return promise
        .then(() => client.getAttributeInVar(ProductSettings.Pagination.products_per_page_input, "value", "pagination"))
        .then(() => {
          global.pagination = Number(Math.trunc(Number(global.productsNumber) / Number(global.tab['pagination'])));
          commonScenarios.checkAllProduct(AccessPageFO, productPage);
        });
    });
  }, 'product/product');

}, 'product/product');
