const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const common_scenarios = require('../02_product/product');
let promise = Promise.resolve();

scenario('Check the sort of products in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog" page', () => {
    return promise
      .then(() => client.waitForExistAndClick(AddProductPage.products_subtab, 2000))
      .then(() => client.getProductsNumber(ProductList.pagination_products));
  });

  common_scenarios.sortProduct(ProductList.product_id, 'id_product');
  common_scenarios.sortProduct(ProductList.product_name, 'name');
  common_scenarios.sortProduct(ProductList.product_reference, 'reference');

  scenario('Back to the default sort', client => {
    test('should click on "Sort by ASC" icon By ID', () => client.waitForExistAndClick(ProductList.sort_by_icon.replace("%B", 'id_product').replace("%W", "asc")));
  }, 'product/product');
}, 'product/product', true);
