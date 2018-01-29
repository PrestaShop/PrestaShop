const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');

scenario('Check default product sort in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog"', () => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu));
  test('should check the default product sort', () => client.getElementID())
}, 'product/product', true);
