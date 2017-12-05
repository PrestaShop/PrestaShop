const {AccessPageBO} = require('../../../selectors/BO/access_page');
scenario('Check order movement', client => {
  test('should open the browser', () => client.open());
  test('should sign in the Back Office', () => client.signInBO(AccessPageBO));
  test('should go to catalog stocks', () => client.goToCatalogStock());
  test('should go to movements tabs', () => client.goToStockMovements());
  test('should check movement history', () => client.checkMovement(1,global.orderQuantity, "-", "Customer Order"));
  test('should sign Out the Back Office', () => client.signOutBO());
}, 'stocks');
