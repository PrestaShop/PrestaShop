/** This script is based on the scenario described in this test link
 * [id="PS-104"][Name="Filters in catalog page"]
 **/
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const commonProduct = require('../../common_scenarios/product');
const promise = Promise.resolve();
const welcomeScenarios = require('../../common_scenarios/welcome');

scenario('Check the sort of products in the Back Office', client => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Close symfony toolbar then change items per page number', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should close symfony toolbar', () => {
      return promise
        .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
    });
    test('should change the paginator select to "100"', () => {
      return promise
        .then(() => client.isVisible(ProductList.pagination_products, 3000))
        .then(() => {
          if (global.isVisible) {
            client.waitAndSelectByValue(ProductList.products_paginator_select, 100);
          }
        })
        .then(() => client.pause(5000))
        .then(() => client.getProductPageNumber('product_catalog_list'));
    });
  }, 'product/product');
  commonProduct.sortProduct(ProductList.products_column.replace('%COL', 2), 'id_product', true);
  commonProduct.sortProduct(ProductList.products_column.replace('%COL', 4), 'name');
  commonProduct.sortProduct(ProductList.products_column.replace('%COL', 5), 'reference');
  commonProduct.sortProduct(ProductList.products_column.replace('%COL', 6), 'name_category');
  commonProduct.sortProduct(ProductList.products_column.replace('%COL', 7), 'price', true, true);
  commonProduct.sortProduct(ProductList.products_column.replace('%COL', 9), 'sav_quantity', true);
  commonProduct.sortProductByStatus();
  scenario('Back to the default sort', client => {
    test('should click on "Sort by DESC" icon By ID', () => {
      return promise
        .then(() => client.pause(7000))
        .then(() => client.moveToObject(ProductList.sort_button.replace('%B', 'id_product')))
        .then(() => client.waitForExistAndClick(ProductList.sort_button.replace('%B', 'id_product')))
        .then(() => client.waitForExistAndClick(ProductList.sort_button.replace('%B', 'id_product')));
    });
  }, 'product/product');
  scenario('Search products by different attributes', () => {
    scenario('Search products by "ID"', client => {
      test('should search products by id', () => {
        return promise
          .then(() => client.isVisible(ProductList.catalogue_filter_by_id_min_input))
          .then(() => client.search(ProductList.catalogue_filter_by_id_min_input, '5'))
          .then(() => client.isVisible(ProductList.catalogue_filter_by_id_max_input))
          .then(() => client.search(ProductList.catalogue_filter_by_id_max_input, '10'))
          .then(() => client.getProductPageNumber('product_catalog_list'));
      });
      commonProduct.productList(AddProductPage, ProductList.products_column.replace('%COL', 2), 'id', client, 5, 10);
    }, 'product/product');
    scenario('Search products by "Name"', client => {
      test('should search products by part of the name', () => {
        return promise
          .then(() => client.isVisible(AddProductPage.catalogue_filter_by_name_input))
          .then(() => client.search(AddProductPage.catalogue_filter_by_name_input, 'mug'))
          .then(() => client.getProductPageNumber('product_catalog_list'));
      });
      commonProduct.productList(AddProductPage, ProductList.products_column.replace('%COL', 4), 'name', client);
    }, 'product/product');
    scenario('Search products by "Reference"', client => {
      test('should search products by reference', () => {
        return promise
          .then(() => client.isVisible(ProductList.catalogue_filter_by_reference_input))
          .then(() => client.search(ProductList.catalogue_filter_by_reference_input, 'demo_1'))
          .then(() => client.getProductPageNumber('product_catalog_list'));
      });
      commonProduct.productList(AddProductPage, ProductList.products_column.replace('%COL', 5), 'reference', client);
    }, 'product/product');
    scenario('Search products by "Category"', client => {
      test('should search products by category', () => {
        return promise
          .then(() => client.isVisible(ProductList.catalogue_filter_by_category_input))
          .then(() => client.search(ProductList.catalogue_filter_by_category_input, 'art'))
          .then(() => client.getProductPageNumber('product_catalog_list'));
      });
      commonProduct.productList(AddProductPage, ProductList.products_column.replace('%COL', 6), 'category', client);
    }, 'product/product');
    scenario('Search products by "Price"', client => {
      test('should search products by price', () => {
        return promise
          .then(() => client.isVisible(ProductList.catalogue_filter_by_price_min_input))
          .then(() => client.search(ProductList.catalogue_filter_by_price_min_input, '18'))
          .then(() => client.isVisible(ProductList.catalogue_filter_by_price_max_input))
          .then(() => client.search(ProductList.catalogue_filter_by_price_max_input, '25'))
          .then(() => client.getProductPageNumber('product_catalog_list'));
      });
      commonProduct.productList(AddProductPage, ProductList.products_column.replace('%COL', 7), 'price', client, 18, 25);
    }, 'product/product');
    scenario('Search products by "Minimum quantity"', client => {
      test('should search a products by minimum quantity', () => {
        return promise
          .then(() => client.isVisible(ProductList.catalogue_filter_by_quantity_min_input))
          .then(() => client.search(ProductList.catalogue_filter_by_quantity_min_input, '600'))
          .then(() => client.getProductPageNumber('product_catalog_list'));
      });
      commonProduct.productList(AddProductPage, ProductList.products_column.replace('%COL', 9), 'min_quantity', client, 600);
    }, 'product/product');
    scenario('Search products by "Quantity"', client => {
      test('should search products by quantity', () => {
        return promise
          .then(() => client.isVisible(ProductList.catalogue_filter_by_quantity_min_input))
          .then(() => client.search(ProductList.catalogue_filter_by_quantity_min_input, '300'))
          .then(() => client.isVisible(ProductList.catalogue_filter_by_quantity_max_input))
          .then(() => client.search(ProductList.catalogue_filter_by_quantity_max_input, '2000'))
          .then(() => client.getProductPageNumber('product_catalog_list'));
      });
      commonProduct.productList(AddProductPage, ProductList.products_column.replace('%COL', 9), 'quantity', client, 300, 2000);
    }, 'product/product');
    scenario('Search products by "Active status"', client => {
      test('should search products by active status', () => {
        return promise
          .then(() => client.waitForExistAndClick(ProductList.status_select))
          .then(() => client.waitForVisibleAndClick(ProductList.catalogue_filter_by_status.replace("%id", 1)))
          .then(() => client.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button))
          .then(() => client.getProductPageNumber('product_catalog_list'));
      });
      commonProduct.productList(AddProductPage, ProductList.products_status_icon, 'active_status', client);
    }, 'product/product');
    scenario('Search products by "Inactive status"', client => {
      test('should search products by inactive status', () => {
        return promise
          .then(() => client.waitForExistAndClick(ProductList.status_select))
          .then(() => client.waitForVisibleAndClick(ProductList.catalogue_filter_by_status.replace("%id", 0)))
          .then(() => client.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button))
          .then(() => client.getProductPageNumber('product_catalog_list'));
      });
      commonProduct.productList(AddProductPage, ProductList.products_status_icon, 'inactive_status', client);
    }, 'product/product');
    scenario('Go back to default pagination', client => {
      test('should go back to default pagination', () => {
        return promise
          .then(() => client.isVisible(ProductList.pagination_products, 3000))
          .then(() => {
            if (global.isVisible) {
              client.waitAndSelectByValue(ProductList.products_paginator_select, 20)
            }
          });
      });
    }, 'product/product');
  }, 'product/product');
}, 'product/product', true);

