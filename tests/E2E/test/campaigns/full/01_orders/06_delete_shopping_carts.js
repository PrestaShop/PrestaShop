/**
 * This script is based on the scenario described in this test link
 * [id="PS-98"][Name="Shopping carts delete"]
 **/

const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../../selectors/FO/order_page');
const {OrderPage, ShoppingCart} = require('../../../selectors/BO/order');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const commonOrder = require('../../common_scenarios/order');
const welcomeScenarios = require('../../common_scenarios/welcome');
let promise = Promise.resolve();

scenario('Delete shopping carts', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Add products to the cart in the Front Office', () => {
    scenario('Go to the Front Office', client => {
      test('should go to the "Front Office"', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1))
          .then(() => client.signInFO(AccessPageFO));
      });
      test('should set the language of shop to "English"', () => client.changeLanguage());
    }, 'order');
    scenario('Add products to the cart', client => {
      test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product));
      test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
      test('should click on "CONTINUE SHOPPING" button', () => client.waitForVisibleAndClick(CheckoutOrderPage.continue_shopping_button));
      test('should go to "Home" page', () => client.waitForExistAndClick(AccessPageFO.logo_home_page, 1000));
      test('should go to the second product page', () => client.waitForExistAndClick(productPage.second_product));
      test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
      test('should click on "CONTINUE SHOPPING" button', () => client.waitForVisibleAndClick(CheckoutOrderPage.continue_shopping_button));
      test('should go to "Home" page', () => client.waitForExistAndClick(AccessPageFO.logo_home_page, 1000));
    }, 'order');
  }, 'order');
  scenario('Delete an abandoned shopping cart', client => {
    test('should go back to the Back office', () => {
      return promise
        .then(() => client.switchWindow(0))
    });
    test('should go to "Shopping cart" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.shopping_carts_submenu));
    test('should check that the cart is not ordered yet', () => client.checkTextValue(OrderPage.check_order_id, 'Non ordered'));
    test('should get the "ID" of the last shopping cart', () => client.getTextInVar(ShoppingCart.id.replace('%NUMBER', 1).replace('%COL', 2), "idShoppingCart"));
    test('should click on "Dropdown" button', () => client.waitForExistAndClick(OrderPage.dropdown_button));
    test('should click on "Delete" action', () => client.waitForExistAndClick(OrderPage.delete_button));
    test('should accept the confirmation alert', () => client.alertAccept());
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
    test('should check that this shopping carts does not appears in the list', () => {
      return promise
        .then(() => client.searchByValue(ShoppingCart.filter_id_input, ShoppingCart.search_button, global.tab['idShoppingCart']))
        .then(() => client.checkTextValue(ShoppingCart.id.replace('%NUMBER', 1).replace('%COL', 1), 'No records found', 'contain'))
        .then(() => client.waitForExistAndClick(ShoppingCart.reset_button));
    });
  }, 'order');
  scenario('Create a new order from the Front Office', client => {
    test('should go back to the Front office', () => {
      return promise
        .then(() => client.switchWindow(1))
    });
    commonOrder.createOrderFO("connected");
  }, 'order');
  scenario('Delete a shopping cart related to an order', client => {
    test('should go back to the Back office', () => {
      return promise
        .then(() => client.switchWindow(0))
        .then(() => client.refresh())
    });
    test('should go to "Shopping cart" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.shopping_carts_submenu));
    test('should check that the "Dropdown" button does not exist', () => client.isNotExisting(OrderPage.first_dropdown_button, 2000));
    test('should check that the "CheckBox" does not exist', () => client.isNotExisting(OrderPage.first_shopping_cart_checkbox, 2000));
  }, 'order');
}, 'order', true);
