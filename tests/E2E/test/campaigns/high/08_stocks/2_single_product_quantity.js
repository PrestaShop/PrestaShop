const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Stock} = require('../../../selectors/BO/catalogpage/stocksubmenu/stock');
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');
const stock_common_scenarios = require('./stock');

scenario('Modify quantity and check movement for single product', client => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');

  scenario('Modify quantity and check movement for single product', client => {
    test('should go to "Stocks" page', () => client.goToSubtabMenuPage(CatalogPage.menu_button, Stock.submenu));
    test('should set the "Search products" input', () => client.waitAndSetValue(Stock.search_input, "FirstProduct"));
    test('should click on "Search" button', () => client.waitForExistAndClick(Stock.search_button));
    stock_common_scenarios.changeStockProductQuantity(client, Stock, 1, 4);
    stock_common_scenarios.checkMovementHistory(client, Movement, 1, "3", "+", "Employee Edition");
    test('should go to "Stock" tab', () => client.waitForExistAndClick(Stock.tabs));
    test('should set the "Search products" input', () => client.waitAndSetValue(Stock.search_input, "SecondProduct"));
    test('should click on "Search" button', () => client.waitForExistAndClick(Stock.search_button));
    stock_common_scenarios.changeStockProductQuantity(client, Stock, 1, 4, "remove");
    stock_common_scenarios.checkMovementHistory(client, Movement, 1, "3", "-", "Employee Edition");
  }, 'stocks');

}, 'stocks', true);