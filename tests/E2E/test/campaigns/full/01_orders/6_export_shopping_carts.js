const {Menu} = require('../../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ShoppingCarts} = require('../../../selectors/BO/order');
const orderCommonScenarios = require('../../common_scenarios/order');
let promise = Promise.resolve();

scenario('Export shopping carts in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');
  scenario('Search for the shopping carts to export', client => {
    test('should go to "Shopping cart" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.shopping_carts_submenu));
    test('should filter by customer "DOE" and carrier "My carrier"', () => {
      return promise
        .then(() => client.waitAndSetValue(ShoppingCarts.search_input.replace('%searchParam', 'c!lastname'), 'DOE'))
        .then(() => client.waitAndSetValue(ShoppingCarts.search_input.replace('%searchParam', 'ca!name'), 'My carrier'))
        .then(() => client.waitForExistAndClick(ShoppingCarts.search_button))
        .then(() => client.getShoppingCartNumber(ShoppingCarts.id_shopping_carts))
        .then(() => orderCommonScenarios.getShoppingCartsInfo())
        .then(() => orderCommonScenarios.checkExportedFile());
    });
  }, 'order');
}, 'order');
