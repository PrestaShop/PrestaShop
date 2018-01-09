const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Stock} = require('../../../selectors/BO/catalogpage/stocksubmenu/stock');
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');

scenario('Check order movement', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');
  
  scenario('Check order movement', client => {
    test('should go to "Stocks"', () => client.goToSubtabMenuPage(CatalogPage.menu_button, Stock.submenu));
    test('should go to "Movements" tabs', () => client.goToStockMovements(Movement));
    test('should check the movements of the delivered product', () => client.checkMovement(Movement, 1, global.tab["orderQuantity"], "-", "Customer Order"));
  }, 'stocks');
}, 'stocks',true);
