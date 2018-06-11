const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const common_scenarios = require('../../common_scenarios/product');
let promise = Promise.resolve();

scenario('Check the sort of products in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog" page', () => {
    return promise
      .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu))
      .then(() => client.getProductPageNumber('product_catalog_list'));
  });

  common_scenarios.sortProduct(ProductList.product_id, 'id_product');
  common_scenarios.sortProduct(ProductList.product_name, 'name');
  common_scenarios.sortProduct(ProductList.product_reference, 'reference');

  scenario('Back to the default sort', client => {
    test('should click on "Sort by DESC" icon By ID', () => client.waitForExistAndClick(ProductList.sort_button.replace("%B", 'id_product')));
  }, 'product/product');
}, 'product/product', true);
