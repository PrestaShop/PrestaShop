const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const common_scenarios = require('../../common_scenarios/product');
let promise = Promise.resolve();

var productData = {
  name: 'CSC',
  quantity: "50",
  price: '10',
  image_name: 'image_test.jpg',
  reference: 'check_shopping'
};

/**
 * This scenario is based on the bug described in this ticket
 * http://forge.prestashop.com/browse/BOOM-3268
 **/

scenario('Check that the shopping cart dosen\'t allow checkout of zero quantity and inactive products', client => {
  test('should open the browser', () => client.open());
  test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  common_scenarios.createProduct(AddProductPage, productData);

  scenario('Test case n°1 : Check that the shopping cart dosen\'t allow checkout of inactive products', client => {
    test('should go to the front office', () => {
      return promise
        .then(() => client.pause(7000))
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1))
    });
    test('should set the shop language to "English"', () => client.changeLanguage('english'));
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should click on "Add to cart" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
    test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
    scenario('Disable the product in the Back Office', client => {
      test('should go back to "Product Settings" page', () => {
        return promise
          .then(() => client.switchWindow(0))
          .then(() => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu))
      });
      test('should search for product by name', () => client.searchProductByName(productData.name + date_time));
      test('should disable the product "' + productData.name + date_time + '"', () => client.waitForExistAndClick(ProductList.first_product_status.replace('%ACTION', 'enabled')));
    }, 'product/check_product');
    scenario('Check that the shopping cart dosen\'t allow checkout in the Front Office', client => {
      test('should go to "Home" page', () => {
        return promise
          .then(() => client.switchWindow(1))
          .then(() => client.waitForExistAndClick(AccessPageFO.logo_home_page))
      });
      test('should click on "Shopping cart" button', () => client.waitForExistAndClick(AccessPageFO.shopping_cart_button));
      test('should check that the proceed to checkout button is disabled when we disable the product', () => {
        return promise
          .then(() => client.isExisting(CheckoutOrderPage.alert))
          .then(() => client.checkAttributeValue(CheckoutOrderPage.proceed_to_checkout_button, 'class', 'disabled', 'contain'))
      });
    }, 'product/product');
  }, 'product/product');

  scenario('Test case n°2 : Check that the shopping cart dosen\'t allow checkout of zero quantity', () => {
    scenario('Enable the product in the Back Office', client => {
      test('should go back to "Product Settings" page', () => {
        return promise
          .then(() => client.switchWindow(0))
          .then(() => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu))
          .then(() => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
      });
      test('should search for product by name', () => client.searchProductByName(productData.name + date_time));
      test('should enable the product "' + productData.name + date_time + '"', () => client.waitForExistAndClick(ProductList.first_product_status.replace('%ACTION', 'disabled')));
    }, 'product/check_product');
    scenario('Edit the product quantity in the Back Office', client => {
      test('should click on "Edit" button of the product' + productData.name + date_time + '"', () => client.waitForExistAndClick(ProductList.edit_button));
      test('should click on "Quantities"', () => client.scrollWaitForExistAndClick(AddProductPage.product_quantities_tab, 50));
      test('should set the "Quantity"', () => client.waitAndSetValue(AddProductPage.product_quantity_input, '-1'));
      test('should click on "SAVE" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
      test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
      test('should go to "Product Settings" page', () => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu));
      test('should reset filter', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    }, 'product/product');
    scenario('Check that the shopping cart dosen\'t allow checkout in the Front Office', client => {
      test('should go to "Home" page', () => {
        return promise
          .then(() => client.switchWindow(1))
          .then(() => client.waitForExistAndClick(AccessPageFO.logo_home_page))
      });
      test('should click on "Shopping cart" button', () => client.waitForExistAndClick(AccessPageFO.shopping_cart_button));
      test('should check that the proceed to checkout button is disabled when the product quantity is equal to "-1"', () => {
        return promise
          .then(() => client.isExisting(CheckoutOrderPage.alert))
          .then(() => client.checkAttributeValue(CheckoutOrderPage.proceed_to_checkout_button, 'class', 'disabled', 'contain'))
      });
    }, 'product/product');
  }, 'product/product');
}, 'product/product', true);
