const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');

scenario('Catalog bulk action', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'catalogbulkaction');

  scenario('Disable the product list', client => {
    test('should go to "Catalog" page', () => client.waitForExistAndClick(CatalogPage.menu_button));
    test('should click on "Select all" radio', () => client.selectAllProducts(CatalogPage.select_all_product_button));
    test('should choose the "Deactivate selection" action', () => client.selectAction(CatalogPage, 2));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct(s) successfully deactivated.'));
    test('should check that the status of the first product is equal to "Clear"', () => client.checkTextValue(CatalogPage.product_status_icon.replace('%S', 1), 'clear'));
    test('should check that the status of the last product is equal to "Clear"', () => client.checkTextValue(CatalogPage.product_status_icon.replace('%S', 7), 'clear'));
  }, 'catalogbulkaction');

  scenario('Enable the product list', client => {
    test('should click on "Select all" radio', () => client.selectAllProducts(CatalogPage.select_all_product_button));
    test('should choose the "Activate selection" action', () => client.selectAction(CatalogPage, 1));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct(s) successfully activated.'));
    test('should check that the status of the first product is equal to "check"', () => client.checkTextValue(CatalogPage.product_status_icon.replace('%S', 1), 'check'));
    test('should check that the status of the last product is equal to "check"', () => client.checkTextValue(CatalogPage.product_status_icon.replace('%S', 7), 'check'));
  }, 'catalogbulkaction');

  scenario('Duplicate the product list', client => {
    test('should click on "Select all" radio', () => client.selectAllProducts(CatalogPage.select_all_product_button));
    test('should choose the "Duplicate selection" action', () => client.selectAction(CatalogPage, 3));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct(s) successfully duplicated.'));
    test('should check that the status of the first product is equal to "clear"', () => client.checkTextValue(CatalogPage.product_status_icon.replace('%S', 1), 'clear'));
    test('should check that the status of the last product is equal to "clear"', () => client.checkTextValue(CatalogPage.product_status_icon.replace('%S', 7), 'clear'));
  }, 'catalogbulkaction');

  scenario('Enable the product list', client => {
    test('should click on "Select all" radio', () => client.selectAllProducts(CatalogPage.select_all_product_button));
    test('should choose the "Activate selection" action', () => client.selectAction(CatalogPage, 1));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct(s) successfully activated.'));
    test('should check that the status of the first product is equal to "check"', () => client.checkTextValue(CatalogPage.product_status_icon.replace('%S', 1), 'check'));
    test('should check that the status of the last product is equal to "check"', () => client.checkTextValue(CatalogPage.product_status_icon.replace('%S', 7), 'check'));
  }, 'catalogbulkaction');

}, 'catalogbulkaction', true);
