const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {productPage} = require('../../../selectors/FO/product_page');
let promise = Promise.resolve();

scenario('Check that all products are well displayed in the Back Office', client => {
  test('should open the browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog"', () => {
    return promise
      .then(() => client.waitForExistAndClick(AddProductPage.products_subtab, 2000))
      .then(() => client.waitAndSelectByValue(ProductList.status_select, '1'))
      .then(() => client.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button))
      .then(() => client.isVisible(ProductList.pagination_products))
      .then(() => client.getProductsNumber(ProductList.pagination_products))
      .then(() => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
  });
  test('should access to the Front Office', () => client.accessToFO(AccessPageFO));
  test('should set the language of shop to "English"', () => client.changeLanguage());
  test('should click on "SEE ALL PRODUCTS" link', () => client.scrollWaitForExistAndClick(productPage.see_all_products));
  test('should check the number of displayed products', () => client.checkTextValue(productPage.products_number, productsNumber, 'contain'));
  test('should check the existence of pagination', () => {
    return promise
      .then(() => client.isVisible(productPage.pagination_next))
      .then(() => client.clickPageNext(productPage.pagination_next))
  });
  test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product_all));
}, 'product/product', true);
