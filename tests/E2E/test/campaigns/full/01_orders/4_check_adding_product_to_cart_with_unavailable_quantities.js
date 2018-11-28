const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');

const common_scenarios = require('../../common_scenarios/product');

let productData = {
  name: 'PQ',
  quantity: "5",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'test_1',
};

/**
 * This scenario is based on the bug described in this ticket
 * http://forge.prestashop.com/browse/BOOM-2805
 **/

scenario('Check adding a product to the cart with unavailable quantities', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');

  common_scenarios.createProduct(AddProductPage, productData);

  scenario('Create order in the Front Office', client => {
    scenario('Open the browser and connect to the Front Office', client => {
      test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
    }, 'order');

    scenario('Check adding unavailable quantities to product in the cart', client => {
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should click on the "Add to cart" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
      test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      test('should set the product quantity to 6', () => client.waitAndSetValue(CheckoutOrderPage.quantity_input.replace('%NUMBER', 1), 6));
      test('should click on "PROCEED TO CHECKOUT" button', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));
      test('should check that the "PROCEED TO CHECKOUT" button is disabled ', () => client.checkAttributeValue(CheckoutOrderPage.proceed_to_checkout_button, 'class', 'disabled', 'contain'));
      test('should click on the product link button', () => client.waitForExistAndClick(CheckoutOrderPage.product_cart_link));
      test('should check the existence of "There are not enough products in stock" warning message', () => client.checkTextValue(productPage.product_availability_message, "Out-of-Stock", "contain"));
    }, 'order');

  }, 'order');

}, 'order', true);
