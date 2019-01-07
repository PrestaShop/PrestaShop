const {Menu} = require('../../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ShoppingCart} = require('../../../selectors/BO/order');
const orderCommonScenarios = require('../../common_scenarios/order');

scenario('Export shopping carts in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');
  scenario('Search for the shopping carts to export', client => {
    test('should go to "Shopping cart" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.shopping_carts_submenu));
    test('should set the "Customer" input', () => client.waitAndSetValue(ShoppingCart.search_input.replace('%searchParam', 'c!lastname'), 'DOE'));
    test('should set the "Carrier" input', () => client.waitAndSetValue(ShoppingCart.search_input.replace('%searchParam', 'ca!name'), 'My carrier'));
    test('should click on the "search" button', () => client.waitForExistAndClick(ShoppingCart.search_button));
    test('should get the "Shopping Carts" number', () => client.getShoppingCartNumber(ShoppingCart.id_shopping_carts));
    test('should get the "Shopping Carts" info', () => orderCommonScenarios.getShoppingCartsInfo(client));
    test('should export the "Shopping Carts" then check the exported file information', () => orderCommonScenarios.checkExportedFile(client));
  }, 'order');
}, 'order', true);
