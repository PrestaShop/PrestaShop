const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {ProductSettings} = require('../../../selectors/BO/shopParameters/product_settings');
const {productPage} = require('../../../selectors/FO/product_page');
const {Menu} = require('../../../selectors/BO/menu.js');

const commonScenarios = require('../../common_scenarios/product');
const commonAttributeScenarios = require('../../common_scenarios/attribute');

let promise = Promise.resolve();

let attributeData = {
  name: 'att',
  public_name: 'att',
  type: 'color',
  values: {
    1: 'red',
    2: 'yellow',
    3: 'green'
  }
};

let productData = [{
  name: 'Standard',
  reference: 'reference',
  quantity: '5',
  price: '10',
  image_name: 'standard.jpg',
  type: "standard"
}, {
  name: 'Pack',
  reference: 'reference',
  quantity: '5',
  price: '10',
  image_name: 'pack.jpeg',
  type: "pack",
  product: {
    name: 'Standard',
    quantity: '1'
  }
}, {
  name: 'Combination',
  reference: 'reference',
  quantity: '5',
  price: '10',
  image_name: 'combination.jpg',
  attribute: {
    name: 'att',
    variation_quantity: '5'
  }
}, {
  name: 'Virtual',
  reference: 'reference',
  quantity: '5',
  price: '10',
  image_name: 'music.jpg',
  type: 'virtual',
  virtual: {
    quantity: '5',
    minimal_quantity: '1'
  }
}];

scenario('Display each kind of product', () => {
  scenario('Create a Standard, Pack, Combination and Virtual product ', client => {
    scenario('Login in the Back Office', client => {
      test('should open the browser', () => client.open());
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'product/product');
      commonScenarios.createProduct(AddProductPage, productData[0]);
      commonScenarios.createProduct(AddProductPage, productData[1]);
      commonAttributeScenarios.createAttribute(attributeData);
      commonScenarios.createProduct(AddProductPage, productData[2]);
      commonScenarios.createProduct(AddProductPage, productData[3]);
  }, 'product/product');

  scenario('Check the product pagination in the Back Office', client => {
    test('should go to "Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should get the product number', () => {
      return promise
        .then(() => client.isVisible(ProductList.pagination_products))
        .then(() => client.getProductsNumber(ProductList.pagination_products))
        .then(() => client.isVisible(AddProductPage.symfony_toolbar, 3000))
        .then(() => {
          if (global.isVisible) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar);
          }
        });
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
          commonScenarios.checkProductInListFO(AccessPageFO, productPage, productData);
        });
    });
  }, 'product/product');

}, 'product/product');
