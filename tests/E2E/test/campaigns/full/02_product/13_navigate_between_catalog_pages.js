const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonProduct = require('../../common_scenarios/product');
let promise = Promise.resolve();
let productData = {
  name: 'Product test',
  quantity: "50",
  price: '500',
  image_name: 'image_test.jpg',
  reference: 'ref123'
};

scenario('Navigate between the catalog pages in the back office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Create products to have more than 20 product in the catalog', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should create products if there\'s less than 20 product in the list', () => commonProduct.checkPaginationThenCreateProduct(client, productData));

  }, 'product/product');
  commonProduct.checkPaginationBO('Next', '1', 20, false, true);
  commonProduct.checkPaginationBO('Next', '1', 50);
  commonProduct.checkPaginationBO('Next', '1', 100, true);
}, 'common_client', true);
