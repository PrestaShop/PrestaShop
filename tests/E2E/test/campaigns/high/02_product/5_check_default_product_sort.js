const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');

scenario('Check default product sort in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog"', () => client.waitForExistAndClick(AddProductPage.products_subtab));
  test('should check the default product sort', () => client.getElementID())
}, 'product/product', true);
