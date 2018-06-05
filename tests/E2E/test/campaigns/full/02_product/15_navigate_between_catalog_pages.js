const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {ProductList} = require('../../../selectors/BO/add_product_page');
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
    test('should create products if there\'s less than 20 product in the list', () => {
      return promise
        .then(() => client.getProductPageNumber('product_catalog_list'))
        .then(() => {
          let productNumber = 20 - global.productsPageNumber;
          if (productNumber !== 0) {
            for (let i = 0; i < productNumber + 1; i++) {
              commonProduct.createProduct(AddProductPage, productData);
            }
          }
        })
        .then(() => commonProduct.checkPaginationBO('Next', '1', 20, false, true))
        .then(() => commonProduct.checkPaginationBO('Next', '1', 50))
        .then(() => commonProduct.checkPaginationBO('Next', '1', 100, true));
    });
  }, 'product/product');
}, 'common_client');
