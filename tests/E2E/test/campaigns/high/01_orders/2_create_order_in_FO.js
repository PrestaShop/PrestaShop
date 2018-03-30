const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {ProductSettings} = require('../../../selectors/BO/shopParameters/product_settings');
const commonOrder = require('../../common_scenarios/order');
const commonProduct = require('../../common_scenarios/product');
let promise = Promise.resolve();

scenario('Create order in the Front Office', () => {
  scenario('Open the browser and connect to the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'order');

    commonOrder.createOrder();

  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'order');
}, 'order', true);

scenario('Check the created order in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');

  commonOrder.checkOrderInBO();

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'order');
}, 'order', true);

/**
 * This scenario is based on the bug described in this ticket
 * http://forge.prestashop.com/browse/BOOM-1965
 */

let productDataOrder = [{
  name: 'productData',
  quantity: "5",
  price: '5',
  image_name: 'image_test.jpg',
  reference: '1'
}];

scenario('Check that Ordering more than the stock is giving a wrong message', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');

  scenario('Change configuration of  "Allow ordering of out-of-stock products" ', client => {
    test('Should go to "Product settings" page', () => client.goToSubtabMenuPage(Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.product_settings_submenu));
    test('Should click on "NO" button to disable ordering of out-of-stock products', () => client.scrollWaitForExistAndClick(ProductSettings.disableOrderOutOfStock_button));
    test('Should click "Save" button', () => client.waitForExistAndClick(ProductSettings.save_button));
  }, 'order');

  scenario('Create a product with the quantity equal to 50', () => {
    commonProduct.createProduct(AddProductPage, productDataOrder[0])
  }, 'order');

  scenario('Connect to the Front Office', client => {
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'order');

  scenario('Order a product from the quick view', client => {
    test('should set the language of shop to "English"', () => client.changeLanguage());
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productDataOrder[0].name + date_time));
    test('should click on "Quick view" button', () => {
      return promise
        .then(() => client.moveToObject(SearchProductPage.product_result_name))
        .then(() => client.waitForExistAndClick(SearchProductPage.quick_view_first_product, 2000))
        .then(() => client.pause(2000))
    });
    test('should set the product quantity to 50', () => client.waitAndSetValue(productPage.first_product_quantity, 50));
    test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(productPage.quick_view_add_to_cart));
    test('should check that the success message does not appear', () => client.checkIsNotVisible(CheckoutOrderPage.success_product_add_to_cart_modal));
  }, 'order');

}, 'order', true);
