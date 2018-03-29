const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const common = require('../../common_scenarios/product');
const {productPage} = require('../../../selectors/FO/product_page');
const commonScenarios = require('../../common_scenarios/discount');

let productData = {
  name: 'SP',
  reference: 'Product with specific price',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  pricing: {
    unitPrice: "10",
    unity: "1",
    wholesale: "5",
    type: 'percentage',
    discount: '19.6'
  }
};

let catalogPriceRule = {
  name: 'discount',
  type: "percentage",
  reduction: '19.666666'
};

/**
 * This scenario is based on the bug described in this ticket
 * http://forge.prestashop.com/browse/BOOM-3838
 **/

scenario('Create "Product"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');
  common.createProduct(AddProductPage, productData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'product/product');
}, 'product/product', true);

scenario('Check "Specific price"', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'product/product');
  scenario('Check the created "Specific price" in the Front Office', client => {
    test('should change front office language to english', () => client.changeLanguage('english'));
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData["name"] + date_time));
    test('should verify that the discount is equal to "-19.67%"', () => client.checkTextValue(SearchProductPage.product_result_discount, '-19.67%'));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should verify that the discount is equal to "-19.67%"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE 19.67%'));
    test('should click on "Add to cart" button  ', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
    test('should click on "Proceed to checkout" button', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
    test('should verify that the discount is equal to "-19.67%"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, '-19.67%'));
  }, 'product/product');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'product/product');
}, 'product/product', true);

scenario('Create "Catalog price rule"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  commonScenarios.createCatalogPriceRules(catalogPriceRule["name"] + date_time, catalogPriceRule["type"], catalogPriceRule["reduction"]);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);

scenario('Check "Catalog price rule"', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'common_client');
  scenario('Check the created "Catalog price rule" in the Front Office', client => {
    test('should change front office language to english', () => client.changeLanguage('english'));
    test('should verify that the discount is equal to "-19.67%"', () => client.checkTextValue(productPage.first_product_discount, '-19.67%'));
    test('should choose product ', () => client.waitForExistAndClick(productPage.first_product));
    test('should verify that the discount is equal to "-19.67%"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, 'SAVE 19.67%'));
    test('should click on "Add to cart" button  ', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
    test('should click on "Proceed to checkout" button', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
    test('should verify that the discount is equal to "-19.67%"', () => client.checkTextValue(CheckoutOrderPage.product_discount_details, '-19.67%'));
  }, 'common_client');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'common_client');
}, 'common_client', true);

scenario('Delete "Catalog price rule"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  commonScenarios.deleteCatalogPriceRules(catalogPriceRule["name"] + date_time);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
