/**
 * This script is based on the scenario described in this test link
 * [id="PS-24"][Name="Display all products"]
 **/
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
    test('should select "active product"', async () => {
      await client.waitAndSelectByValue(ProductList.status_filter, '1');
      await page.click(AddProductPage.catalogue_submit_filter_button);
      await page.waitForNavigation();
    });
    test('should sort by product Name', async () => {
      await page.click(ProductList.sort_by_icon.replace('%B','name'));
      await page.waitForNavigation();
    })
    test('should get the product number', () => {
      return promise
        .then(() => client.isVisible(ProductList.pagination_products))
        .then(() => client.getProductsNumber(ProductList.pagination_products))
        .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
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

    test('should get all product\'s name', async () => {
      for (let i = 1; i <= global.productsNumber; i++) {
        client.getProductName(ProductList.product_name.replace('%ID', i));
      }
    });


    test('should go to "Shop Parameters - Product Settings" page', () => {
      return promise
        .then(() => client.pause(3000))
        .then(() => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.product_settings_submenu));
    });
    test('should get the pagination Products per page value and check the created product in the Front Office', async () => {
      await client.getAttributeInVar(ProductSettings.Pagination.products_per_page_input, "value", "pagination");
      global.pagination = await Number(Math.trunc(Number(global.productsNumber) / Number(global.tab['pagination'])));
      await commonScenarios.checkAllProduct(AccessPageFO, productPage, client);
    });
  }, 'product/product');
}, 'product/product', true);

