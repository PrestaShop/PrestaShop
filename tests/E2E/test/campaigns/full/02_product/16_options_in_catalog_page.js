const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Menu} = require('../../../selectors/BO/menu.js');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
let promise = Promise.resolve();

scenario('Check the options in the catalog page', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Disable the first product from the list in the Back Office', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should disable the first product', () => client.waitForExistAndClick(ProductList.product_status.replace('%I', 1).replace('%ACTION', 'enabled')));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct successfully deactivated.'));
    test('should check that the status of the first product is equal to "Clear"', () => client.checkTextValue(CatalogPage.product_status_icon.replace('%S', 1), 'clear'));
  }, 'common_client');
  scenario('Enable the first product from the list in the Back Office', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should disable the first product', () => client.waitForExistAndClick(ProductList.product_status.replace('%I', 1).replace('%ACTION', 'disabled')));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct successfully activated.'));
    test('should check that the status of the first product is equal to "Check"', () => client.checkTextValue(CatalogPage.product_status_icon.replace('%S', 1), 'check'));
  }, 'common_client');
  scenario('Preview the first product from the list in the Back Office', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should click on "Dropdown > Preview" button', () => {
      return promise
        .then(() => client.getTextInVar(ProductList.product_name.replace('%ID', 1), 'productName'))
        .then(() => client.waitForExistAndClick(ProductList.dropdown_button.replace('%POS', 1)))
        .then(() => client.waitForVisibleAndClick(ProductList.action_preview_button.replace('%POS', 1)))
        .then(() => client.switchWindow(1));
    });
    test('should check that the first product is well opened', () => {
      return promise
        .then(() => client.checkTextValue(productPage.product_name, tab['productName'].toUpperCase()))
        .then(() => client.switchWindow(0));
    });
  }, 'common_client');
  scenario('Duplicate the first product from the list in the Back Office', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should click on "Dropdown > Duplicate" button', () => {
      return promise
        .then(() => client.waitForExistAndClick(ProductList.dropdown_button.replace('%POS', 1)))
        .then(() => client.waitForVisibleAndClick(ProductList.action_duplicate_button.replace('%POS', 1)));
    });
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct successfully duplicated.', 'equal', 3000));
    test('should check that the first product is well duplicated', () => client.checkAttributeValue(AddProductPage.product_name_input, 'value', 'copy', 'contain'));
  }, 'common_client');
  scenario('Delete the first product from the list in the Back Office', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should search for the duplicated product', () => client.waitAndSetValue(CatalogPage.name_search_input, 'copy'));
    test('should click on the "ENTER" key', () => client.keys('Enter'));
    test('should click on "Dropdown > Delete" button', () => {
      return promise
        .then(() => client.getTextInVar(ProductList.product_name.replace('%ID', 1), 'duplicatedProductName'))
        .then(() => client.waitForExistAndClick(ProductList.dropdown_button.replace('%POS', 1)))
        .then(() => client.waitForVisibleAndClick(ProductList.action_delete_button.replace('%POS', 1)))
        .then(() => client.waitForVisibleAndClick(ProductList.delete_now_modal_button));
    });
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct successfully deleted.'));
    test('should check that the first product is well deleted', () => {
      return promise
        .then(() => client.searchProductByName(tab['duplicatedProductName']))
        .then(() => client.checkTextValue(ProductList.search_no_results, 'There is no result for this search. Update your filters to view other products.'))
        .then(() => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    });
  }, 'product/check_product');
}, 'common_client', true);
