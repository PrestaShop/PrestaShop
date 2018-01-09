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
    stock_common_scenarios.changeStockProductQuantity(client, Stock, 3, 4);
    stock_common_scenarios.checkMovementHistory(client, Movement, 1, "3", "+", "Employee Edition" );
    test('should go to "Stock" tab', () => client.waitForExistAndClick(Stock.tabs));
    stock_common_scenarios.changeStockProductQuantity(client, Stock, 4, 4, "remove");
    stock_common_scenarios.checkMovementHistory(client, Movement, 1, "3", "-", "Employee Edition" );
  }, 'stocks');
}, 'stocks', true);
