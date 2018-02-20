const common_scenarios = require('../high/02_product/product');
const {AccessPageBO} = require('../../selectors/BO/access_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {productPage} = require('../../selectors/FO/product_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const {SearchProductPage} = require('../../selectors/FO/search_product_page');
let promise = Promise.resolve();

var productData = {
  name: 'Dress',
  quantity: "10",
  price: '5',
  image_name: '1.png',
  reference: 'robe'
};

scenario('Create "Product"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_scenarios.createProduct(AddProductPage, productData);
  common_scenarios.checkProductBO(AddProductPage, productData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);

scenario('Check the created product in the Front Office', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'product/product');
  scenario('Check that the created product is well displayed in the Front Office', client => {
    test('should set the shop language to "English"', () => client.changeLanguage('english'));
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should check that the product name is equal to "' + (productData.name + date_time).toUpperCase() + '"', () => client.checkTextValue(productPage.product_name, (productData.name + date_time).toUpperCase()));
    test('should check that the product price is equal to "€6.00"', () => client.checkTextValue(productPage.product_price, '€6.00'));
    test('should check that the product reference is equal to "' + productData.reference + '"', () => {
      return promise
        .then(() => client.scrollTo(productPage.product_reference))
        .then(() => client.checkTextValue(productPage.product_reference, productData.reference))
    });
    test('should check that the product quantity is equal to "10"', () => client.checkAttributeValue(productPage.product_quantity, 'data-stock', productData.quantity));
  }, 'product/product');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => {
      return promise
        .then(() => client.scrollTo(AccessPageFO.sign_out_button))
        .then(() => client.signOutFO(AccessPageFO))
    });
  }, 'product/product');
}, 'product/product', true);
