const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Menu} = require('../../../selectors/BO/menu.js');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const welcomeScenarios = require('../../common_scenarios/welcome');

let promise = Promise.resolve();

scenario('Catalog bulk action', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'catalogbulkaction');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Disable the product list', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should click on "Select all" radio button', () => client.selectAllProducts(CatalogPage.select_all_product_button));
    test('should choose the "Deactivate selection" action', () => client.selectAction(CatalogPage, 2));
    test('should Verify that the modal "deactivation in progress" is well displayed', () => {
      return promise
        .then(() => client.isVisibleWithinViewport(CatalogPage.deactivate_modal))
        .then(() => client.waitForVisible(CatalogPage.green_validation, 90000));
    });
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct(s) successfully deactivated.'));
    test('should get the products page number', () => client.getProductPageNumber('product_catalog_list'));
    test('should verify that all products statuses are disabled successfully', () => {
      for (let j = 0; j < global.productsPageNumber; j++) {
        promise = client.getProductStatus(CatalogPage.product_status_icon.replace('%S', j + 1), j);
        promise = client.pause(2000);
      }
      return promise
        .then(() => expect(global.productStatus).to.not.include("check"));
    });
  }, 'catalogbulkaction');

  scenario('Enable the product list', client => {
    test('should click on "Select all" radio button', () => client.selectAllProducts(CatalogPage.select_all_product_button));
    test('should choose the "Activate selection" action', () => client.selectAction(CatalogPage, 1));
    test('should Verify that the modal "activation in progress" is well displayed', () => {
      return promise
        .then(() => client.isVisibleWithinViewport(CatalogPage.activate_modal))
        .then(() => client.waitForVisible(CatalogPage.green_validation, 90000));
    });
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct(s) successfully activated.'));
    test('should get the products page number', () => client.getProductPageNumber('product_catalog_list'));
    test('should verify that all products statuses are enabled successfully', () => {
      for (let j = 0; j < global.productsPageNumber; j++) {
        promise = client.getProductStatus(CatalogPage.product_status_icon.replace('%S', j + 1), j);
        promise = client.pause(2000);
      }
      return promise
        .then(() => expect(global.productStatus).to.not.include("clear"));
    });
  }, 'catalogbulkaction');

  scenario('Duplicate the product list', client => {
    test('should click on "Select all" radio button', () => {
      return promise
        .then(() => client.isVisible(ProductList.pagination_products))
        .then(() => client.getProductsNumber(ProductList.pagination_products))
        .then(() => client.selectAllProducts(CatalogPage.select_all_product_button));
    });
    test('should choose the "Duplicate selection" action', () => client.selectAction(CatalogPage, 3));
    test('should Verify that the modal "Duplication in progress" is well displayed', () => {
      return promise
        .then(() => client.isVisibleWithinViewport(CatalogPage.duplicate_modal, 1000))
        .then(() => client.waitForVisible(CatalogPage.green_validation, 90000));
    });
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct(s) successfully duplicated.'));
    test('should check that the products were duplicated', () => {
      let number = typeof global.productsNumber !== 'undefined' ? parseInt(global.productsNumber) : 0;
      return promise
        .then(() => client.getProductPageNumber('product_catalog_list'))
        .then(() => client.isVisible(ProductList.pagination_products))
        .then(() => {
          if (global.isVisible) {
            return promise
              .then(() => client.checkTextValue(ProductList.pagination_products, parseInt(global.productsNumber) + number, 'contain'));
          }
        });
    });
  }, 'catalogbulkaction');

  scenario('Delete duplicated products list with bulk action', client => {
    scenario('Abort the delete product action from the modal', client => {
      test('should set the search input to "copy" to search for the duplicated products', () => client.waitAndSetValue(CatalogPage.name_search_input, "copy"));
      test('should click on the "ENTER" key', () => client.keys('Enter'));
      test('should click on "Select all" radio button', () => client.selectAllProducts(CatalogPage.select_all_product_button));
      test('should click on the "Bulk actions" button', () => client.waitForExistAndClick(CatalogPage.action_group_button));
      test('should click on the "Delete selection" button', () => client.waitForExistAndClick(CatalogPage.action_button.replace("%ID", 4)));
      test('should click on the "Close" button', () => client.waitForVisibleAndClick(CatalogPage.close_delete_modal));
      scenario('Check that duplicate products are not deleted', client => {
        test('should set the search input to "copy" to search for the duplicated products', () => client.waitAndSetValue(CatalogPage.name_search_input, "copy", 2000));
        test('should click on the "ENTER" key', () => client.keys('Enter'));
        test('should check the existence of the list of products', () => client.isNotExisting(CatalogPage.search_result_message, 2000));
        test('should click on "Reset" button', () => client.waitForVisibleAndClick(CatalogPage.reset_button, 2000));
      }, 'catalogbulkaction');
    }, 'catalogbulkaction');

    scenario('Check when accepting to delete duplicated product modal', client => {
      test('should set the search input to "copy" to search for the duplicated products', () => client.waitAndSetValue(CatalogPage.name_search_input, "copy"));
      test('should click on the "ENTER" key', () => client.keys('Enter'));
      test('should click on "Select all" radio button', () => client.selectAllProducts(CatalogPage.select_all_product_button));
      test('should click on the "Bulk actions" button', () => client.waitForExistAndClick(CatalogPage.action_group_button));
      test('should click on the "Delete selection" button', () => client.waitForExistAndClick(CatalogPage.action_button.replace("%ID", 4)));
      test('should click on the "delete now" button', () => client.waitForVisibleAndClick(CatalogPage.delete_confirmation));
      test('should Verify that the modal "Deletion in progress" is well displayed', () => {
        return promise
          .then(() => client.isVisibleWithinViewport(CatalogPage.delete_modal, 1000))
          .then(() => client.waitForVisible(CatalogPage.green_validation, 90000));
      });
      test('should check that the duplicate products are deleted successfully', () => {
        return promise
          .then(() => client.waitForVisible(CatalogPage.green_validation, 90000))
          .then(() => client.checkTextValue(CatalogPage.green_validation, 'close\nProduct(s) successfully deleted.'));
      });
      test('should click on "Reset" button', () => client.waitForVisibleAndClick(CatalogPage.reset_button));
      scenario('Check that the duplicate product has been deleted', client => {
        test('should set the search input to "copy" to search for the duplicated products', () => client.waitAndSetValue(CatalogPage.name_search_input, "copy"));
        test('should click on the "ENTER" key', () => client.keys('Enter'));
        test('should get a message indicates that no result found', () => client.checkTextValue(CatalogPage.search_result_message, 'There is no result for this search', "contain", 2000));
        test('should click on "Reset" button', () => client.waitForVisibleAndClick(CatalogPage.reset_button));
      }, 'catalogbulkaction');
    }, 'catalogbulkaction');
  }, 'catalogbulkaction');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'product/product');

}, 'catalogbulkaction', true);
