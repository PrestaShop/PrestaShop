const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Menu} = require('../../../selectors/BO/menu.js');
const {ProductList} = require('../../../selectors/BO/add_product_page');
let promise = Promise.resolve();

scenario('Catalog bulk action', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'catalogbulkaction');

  scenario('Disable the product list', client => {
    test('should go to "Catalog" page', () => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu));
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
    test('should click on "Select all" radio', () => {
      return promise
        .then(() => client.isVisible(ProductList.pagination_products))
        .then(() => client.getProductsNumber(ProductList.pagination_products))
        .then(() => client.selectAllProducts(CatalogPage.select_all_product_button));
    });
    test('should choose the "Duplicate selection" action', () => client.selectAction(CatalogPage, 3));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct(s) successfully duplicated.'));
    test('should check that the products were duplicated', () => {
      let number = typeof global.productsNumber !== 'undefined' ? parseInt(global.productsNumber) : 0;
      return promise
        .then(() => client.getProductPageNumber('product_catalog_list'))
        .then(() => client.checkTextValue(ProductList.pagination_products, parseInt(global.productsPageNumber) + number, 'contain'));
    });
  }, 'catalogbulkaction');

  scenario('delete the duplicated products list with bulk action', client => {
    test('should set the search input to "copy" to search for the duplicated products', () => client.waitAndSetValue(CatalogPage.name_search_input, "copy"));
    test('should click on the "ENTER" key', () => client.keys('Enter'));
    test('should click on "Select all" radio', () => client.selectAllProducts(CatalogPage.select_all_product_button));
    test('should click on the "Bulk actions" button', () => client.waitForExistAndClick(CatalogPage.action_group_button));
    test('should click on the "Delete selection" button', () => client.waitForExistAndClick(CatalogPage.action_button.replace("%ID", 4)));
    test('should click on the "delete now" button', () => client.waitForVisibleAndClick(CatalogPage.delete_confirmation));
    test('should verify the appearance of the green validation message', () => {
      return promise
        .then(() => client.waitForVisible(CatalogPage.green_validation, 90000))
        .then(() => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct(s) successfully deleted.'));
    });
    test('should click on "Reset" button', () => client.waitForVisibleAndClick(CatalogPage.reset_button));
    scenario('should check that the duplicate product has been deleted', client => {
      test('should set the search input to "copy" to search for the duplicated products', () => client.waitAndSetValue(CatalogPage.name_search_input, "copy"));
      test('should click on the "ENTER" key', () => client.keys('Enter'));
      test('should get a message indicates that no result found', () => client.checkTextValue(CatalogPage.search_result_message, 'There is no result for this search', "contain"));
      test('should click on "Reset" button', () => client.waitForVisibleAndClick(CatalogPage.reset_button));
    }, 'catalogbulkaction');

  }, 'catalogbulkaction');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'product/product');

}, 'catalogbulkaction', true);