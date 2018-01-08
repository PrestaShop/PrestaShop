const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ShopParametre} = require('../../../selectors/BO/shopParameters/');
const {ProductSettings} = require('../../../selectors/BO/shopParameters/product_settings');
const {productPage}= require('../../../selectors/FO/product_page');
const {buyOrderPage}= require('../../../selectors/FO/buy_order_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {layerCart}= require('../../../selectors/FO/layer_cart_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const common_scenarios = require('../2_product/product');

productData = [{
  name: 'productA',
  quantity: "50",
  price: '5',
  image_name: 'image_test.jpg',
}, {
  type: "pack",
  name: 'productB',
  quantity: "5",
  price: '5',
  image_name: 'image_test.jpg',
  product: {
    name: "productA",
    quantity: "10"
  }
}];

scenario('Create standard product "A" and pack product "B"', client => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');
  scenario('Change configuration of "Default pack stock management" and "Allow ordering of out-of-stock products"', client => {
    test('Should go to "Product settings" page', () => client.goToSubtabMenuPage(ShopParametre.menu_button, ProductSettings.menu));
    test('Should click on "NO" button to disable ordering of out-of-stock products', () => client.scrollWaitForExistAndClick(ProductSettings.disableOrderOutOfStock_button));
    test('Should select "Decrement both" of "Default pack stock management"', () => client.waitAndSelectByValue(ProductSettings.stockManagement_button, 2));
    test('Should click "Save" button', () => client.waitForExistAndClick(ProductSettings.save_button));
  }, 'product/product');
  common_scenarios.createProduct(AddProductPage, productData[0]);
  common_scenarios.createProduct(AddProductPage, productData[1]);
}, 'product/product', true);

scenario('Check "Orders"', client => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'product/product');
  scenario('Create order with 50 item of product A', client => {
    test('should change the FO language to english', () => client.changeLanguage());
    test('should search for the product "A"', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData[0]['name']));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should set the product "quantity"', () => client.waitAndSetValue(productPage.first_product_quantity, "50"));
    test('should click on "Add to cart" button  ', () => client.waitForExistAndClick(buyOrderPage.add_to_cart_button));
    test('should verify the appearance of the green confirmation', () => client.checkGreenConfirmation());
    test('should click on proceed to checkout button', () => client.waitForExistAndClick(layerCart.command_button));
  }, 'order/order');
  scenario('Create order with 10 item of pack B ', client => {
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData[1]['name']));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should verify that the button "AJOUTER AU PANIER" is DISABLED', () => client.checkEnable(buyOrderPage.add_to_cart_button));
  }, 'order/order');
}, 'product/product',true);
