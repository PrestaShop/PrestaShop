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
  common_scenarios.sortProduct(ProductList.product_category, 'name_category');
  common_scenarios.sortProduct(ProductList.product_price, 'price');
  common_scenarios.sortProduct(ProductList.product_quantity, 'sav_quantity');
  common_scenarios.sortProduct(ProductList.product_status, 'active');

  scenario('Back to the default sort', client => {
    test('should click on "Sort by DESC" icon By ID', () => client.waitForExistAndClick(ProductList.sort_button.replace("%B", 'id_product')));
  }, 'product/product');
}, 'product/product');
scenario('Search products by different attributes', () => {
  scenario('Search products  by "ID"', client => {
    test('should search products by id', () => {
      return promise
        .then(() => client.isVisible(AddProductPage.catalogue_filter_by_id_min_input))
        .then(() => client.search(AddProductPage.catalogue_filter_by_id_min_input, '5'))
        .then(() => client.isVisible(AddProductPage.catalogue_filter_by_id_max_input))
        .then(() => client.search(AddProductPage.catalogue_filter_by_id_max_input, '10'))
        .then(() => client.getProductPageNumber('product_catalog_list'));
    });
    common_scenarios.productList(AddProductPage, ProductList.product_id, 'id', 5, 10);
  }, 'product/product');
  scenario('Search products by "Name"', client => {
    test('should search products by part of the name', () => {
      return promise
        .then(() => client.isVisible(AddProductPage.catalogue_filter_by_name_input))
        .then(() => client.search(AddProductPage.catalogue_filter_by_name_input, 'mug'))
        .then(() => client.getProductPageNumber('product_catalog_list'));
    });
    common_scenarios.productList(AddProductPage, ProductList.product_name, 'name');
  }, 'product/product');
  scenario('Search products by "Reference"', client => {
    test('should search products by reference', () => {
      return promise
        .then(() => client.isVisible(AddProductPage.catalogue_filter_by_reference_input))
        .then(() => client.search(AddProductPage.catalogue_filter_by_reference_input, 'demo_1'))
        .then(() => client.getProductPageNumber('product_catalog_list'));
    });
    common_scenarios.productList(AddProductPage, ProductList.product_reference, 'reference');
  }, 'product/product');
  scenario('Search products by "Category"', client => {
    test('should search products by category', () => {
      return promise
        .then(() => client.isVisible(AddProductPage.catalogue_filter_by_category_input))
        .then(() => client.search(AddProductPage.catalogue_filter_by_category_input, 'art'))
        .then(() => client.getProductPageNumber('product_catalog_list'));
    });
    common_scenarios.productList(AddProductPage, ProductList.product_category, 'category');
  }, 'product/product');
  scenario('Search products by "Price"', client => {
    test('should search products by price', () => {
      return promise
        .then(() => client.isVisible(AddProductPage.catalogue_filter_by_price_min_input))
        .then(() => client.search(AddProductPage.catalogue_filter_by_price_min_input, '18'))
        .then(() => client.isVisible(AddProductPage.catalogue_filter_by_price_max_input))
        .then(() => client.search(AddProductPage.catalogue_filter_by_price_max_input, '25'))
        .then(() => client.getProductPageNumber('product_catalog_list'));
    });
    common_scenarios.productList(AddProductPage, ProductList.product_price, 'price', 18, 25);
  }, 'product/product');
  scenario('Search products  by "Minimum quantity"', client => {
    test('should search a products by minimum quantity', () => {
      return promise
        .then(() => client.isVisible(AddProductPage.catalogue_filter_by_quantity_min_input))
        .then(() => client.search(AddProductPage.catalogue_filter_by_quantity_min_input, '300'))
        .then(() => client.getProductPageNumber('product_catalog_list'));
    });
    common_scenarios.productList(AddProductPage, ProductList.product_quantity, 'min_quantity', 300);
  }, 'product/product');
  scenario('Search products by "Quantity"', client => {
    test('should search products by quantity', () => {
      return promise
        .then(() => client.isVisible(AddProductPage.catalogue_filter_by_quantity_min_input))
        .then(() => client.search(AddProductPage.catalogue_filter_by_quantity_min_input, '300'))
        .then(() => client.isVisible(AddProductPage.catalogue_filter_by_quantity_max_input))
        .then(() => client.search(AddProductPage.catalogue_filter_by_quantity_max_input, '2000'))
        .then(() => client.getProductPageNumber('product_catalog_list'));
    });
    common_scenarios.productList(AddProductPage, ProductList.product_quantity, 'quantity', 300, 2000);
  }, 'product/product');
  scenario('Search products by "Active status"', client => {
    test('should search products by active status', () => {
      return promise
        .then(() => client.waitForExistAndClick(ProductList.status_select))
        .then(() => client.waitForVisibleAndClick(AddProductPage.catalogue_filter_by_status.replace("%id", 1)))
        .then(() => client.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button))
        .then(() => client.getProductPageNumber('product_catalog_list'));
    });
    common_scenarios.productList(AddProductPage, ProductList.product_status, 'active_status');
  }, 'product/product');
  scenario('Search products  by "Inactive status"', client => {
    test('should disable the first product', () => client.waitForExistAndClick(ProductList.change_product_status.replace("%ID", 1)));
    test('should search products by inactive status', () => {
      return promise
        .then(() => client.waitForExistAndClick(ProductList.status_select))
        .then(() => client.waitForVisibleAndClick(AddProductPage.catalogue_filter_by_status.replace("%id", 0)))
        .then(() => client.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button))
        .then(() => client.getProductPageNumber('product_catalog_list'));
    });
    common_scenarios.productList(AddProductPage, ProductList.product_status, 'inactive_status');
  }, 'product/product');
}, 'product/product', true);
