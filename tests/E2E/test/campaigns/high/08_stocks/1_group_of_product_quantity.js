const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Stock} = require('../../../selectors/BO/catalogpage/stocksubmenu/stock');
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');
let promise = Promise.resolve();

scenario('Modify quantity and check the movement of a group of product', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');
  scenario('Modify quantity and check the movement of a group of product', client => {
    test('should go to "Stocks" page', () => client.goToSubtabMenuPage(CatalogPage.menu_button, Stock.submenu));
    test('should set the "Quantity" of the first product to 15', () => client.modifyProductQuantity(Stock, 2, 15));
    test('should set the "Quantity" of the second product to 50', () => client.modifyProductQuantity(Stock, 1, 50));
    test('should click on "Apply new quantity" button', () => client.waitForExistAndClick(Stock.group_apply_button));
    test('should click on "Movements" tab', () => client.goToStockMovements(Movement));
    test('should verify the new "Quantity" and "Type" of the two changed products', () => {
      return promise
        .then(() => client.getTextInVar(Movement.time_movement.replace('%P', 1), 'firstMovementDate'))
        .then(() => client.getTextInVar(Movement.time_movement.replace('%P', 2), 'secondMovementDate'))
        .then(() => client.checkOrderMovement(Movement, client));
    });
  }, 'stocks');
}, 'stocks', true);
