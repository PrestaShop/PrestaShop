/**
 * This script is based on the scenario described in this test link
 * [id="PS-22"][Name="Bulk action in catalog page"]
 **/
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Menu} = require('../../../selectors/BO/menu.js');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const welcomeScenarios = require('../../common_scenarios/welcome');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
global.isPagination = false;

let promise = Promise.resolve();

scenario('Bulk action in catalog page', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'catalogbulkaction');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Disable the product list', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should click on "Select all" radio button', async () => await client.selectAllProducts(CatalogPage.select_all_product_button));
    test('should choose the "Deactivate selection" action', async () => await client.selectAction(CatalogPage, 2));
    test('should Verify that the modal "deactivation in progress" is well displayed', async () => {
      return promise
        .then(async () => await page.waitForSelector(CatalogPage.deactivate_modal + '.modal.show'))
        .then(async () => await page.waitForSelector(CatalogPage.deactivate_modal + '.modal:not(.show)'))
        .then(async () => await page.waitForSelector(CatalogPage.green_validation,{visible:'true'}));
    });
    test('should verify the appearance of the green validation', () => client.checkTextContent(CatalogPage.green_validation, 'Product(s) successfully deactivated.'));
    test('should get the products page number', () => client.getProductPageNumber('#product_catalog_list'));
    test('should verify that all products statuses are disabled successfully', () => {
      for (let j = 0; j < global.productsNumber; j++) {
        promise = client.getProductStatus(CatalogPage.product_status_icon.replace('%S', j + 1), j);
        promise = client.pause(2000);
      }
      return promise
        .then(() => expect(global.productStatus).to.not.include("check"));
    });
    test('should get the "Name" of the last inactive product', () => client.getTextInVar(ProductList.products_column.replace('%ID', 1).replace('%COL', 4), 'productName'));
  }, 'catalogbulkaction');
  scenario('Check existence product in Front Office', client => {
    test('should go to the "Front Office"', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1))
        .then(() => client.changeLanguage());
    });
    test('should search a disabled product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, global.tab['productName']));
    test('should check the not existence of the disabled product', () => page.waitForSelector(SearchProductPage.empty_result_section));
    test('should go back to the Back Office', () => client.switchWindow(0));
  }, 'catalogbulkaction');
  scenario('Enable the product list', client => {
    test('should click on "Select all" radio button', () => client.selectAllProducts(CatalogPage.select_all_product_button));
    test('should choose the "Activate selection" action', () => client.selectAction(CatalogPage, 1));
    test('should Verify that the modal "activation in progress" is well displayed', () => {
      return promise
        .then(async () => await page.waitForSelector(CatalogPage.activate_modal + '.modal.show'))
        .then(async () => await page.waitForSelector(CatalogPage.activate_modal + '.modal:not(.show)'))
        .then(async () => await page.waitForSelector(CatalogPage.green_validation,{visible:'true'}));
    });
    test('should verify the appearance of the green validation', () => client.checkTextContent(CatalogPage.green_validation, 'Product(s) successfully activated.'));
    test('should get the products page number', () => client.getProductPageNumber('#product_catalog_list'));
    test('should verify that all products statuses are enabled successfully', () => {
      for (let j = 0; j < global.productsNumber; j++) {
        promise = client.getProductStatus(CatalogPage.product_status_icon.replace('%S', j + 1), j);
        promise = client.pause(2000);
      }
      return promise
        .then(() => expect(global.productStatus).to.not.include('clear'));
    });
    test('should get the "Name" of the last active product', () => client.getTextInVar(ProductList.products_column.replace('%ID', 1).replace('%COL', 4), 'productName'));
  }, 'catalogbulkaction');
  scenario('Check existence product in Front Office', client => {
    test('should go to the "Front Office"', () => client.switchWindow(1));
    test('should search an enabled product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, global.tab['productName']));
    test('should check the existence of the active product', () => client.checkExistenceProduct(SearchProductPage.first_product_name_link, global.tab['productName'].toLowerCase()));
    test('should go back to the Back Office', () => client.switchWindow(0));
  }, 'catalogbulkaction');
  scenario('Duplicate the product list', client => {
    test('should click on "Select all" radio button', async () => {
      return promise
        .then(() => client.isVisible(ProductList.pagination_products))
        .then(() => {
          if (global.isVisible) {
            global.isPagination = true;
          }
        })
        .then(() => client.getProductsNumber(ProductList.pagination_products))
        .then(async () => await client.selectAllProducts(CatalogPage.select_all_product_button));
    });
    test('should choose the "Duplicate selection" action', () => client.selectAction(CatalogPage, 4));
    test('should Verify that the modal "Duplication in progress" is well displayed', async () => {
      return promise
        .then(async () => await page.waitForSelector(CatalogPage.duplicate_modal + '.modal.show'))
        .then(async () => await page.waitForSelector(CatalogPage.duplicate_modal + '.modal:not(.show)'))
        .then(async () => await page.waitForSelector(CatalogPage.green_validation,{visible:'true'}));
    });
    test('should verify the appearance of the green validation', () => client.checkTextContent(CatalogPage.green_validation, 'Product(s) successfully duplicated.'));
    test('should check that the products were duplicated', () => {
      let number = typeof global.productsNumber !== 'undefined' ? parseInt(global.productsNumber) : 0;
      return promise
        .then(() => client.isVisible(ProductList.pagination_products))
        .then(() => {
          if (global.isVisible) {
            if (global.isPagination) {
              return promise
                .then(() => client.checkTextValue(ProductList.pagination_products, parseInt(global.productsNumber) + 20, 'contain', 2000));

            } else {
              return promise
                .then(() => client.checkTextValue(ProductList.pagination_products, parseInt(global.productsNumber) + number, 'contain', 2000));
            }
          }
        });
    });
  }, 'catalogbulkaction');
  scenario('Delete duplicated products list with bulk action', () => {
    scenario('Abort the delete product action from the modal', client => {
      test('should set the search input to "copy" to search for the duplicated products', () => client.waitAndSetValue(CatalogPage.name_search_input, "copy"));
      test('should click on the "ENTER" key', async () => {
        await client.keys('Enter');
        await page.waitForNavigation();
      });
      test('should click on "Select all" radio button', async () => await client.selectAllProducts(CatalogPage.select_all_product_button));
      test('should click on the "Bulk actions" button', async () => await client.waitForExistAndClick(CatalogPage.action_group_button));
      test('should click on the "Delete selection" button', async () => await client.waitForExistAndClick(CatalogPage.action_button.replace("%ID", 6)));
      test('should click on the "Close" button', () => client.waitForExistAndClick(CatalogPage.close_delete_modal,1000));
      scenario('Check that duplicate products are not deleted', client => {
        test('should set the search input to "copy" to search for the duplicated products', () => client.fillInputText(CatalogPage.name_search_input, "copy"));
        test('should click on the "ENTER" key', async () => {
          await client.keys('Enter');
          await page.waitForNavigation();
        });
        test('should check the existence of the list of products', async () =>  {
          let text = await client.getText(CatalogPage.search_result_message);
          expect(text).to.not.contains('Product(s) successfully deleted.');
        });
        test('should click on "Reset" button', async () => {
          await client.waitForVisibleAndClick(CatalogPage.reset_button);
          await page.waitForNavigation();
        });
      }, 'catalogbulkaction');
    }, 'catalogbulkaction');

    scenario('Check when accepting to delete duplicated product modal', client => {
      test('should set the search input to "copy" to search for the duplicated products', () => client.fillInputText(CatalogPage.name_search_input, "copy"));
      test('should click on the "ENTER" key', async () => {
        await client.keys('Enter');
        await page.waitForNavigation();
      });
      test('should click on "Select all" radio button', async () => await client.selectAllProducts(CatalogPage.select_all_product_button));
      test('should click on the "Bulk actions" button', () => client.waitForExistAndClick(CatalogPage.action_group_button));
      test('should click on the "Delete selection" button', () => client.waitForExistAndClick(CatalogPage.action_button.replace("%ID", 6)));
      test('should click on the "delete now" button', () => client.waitForVisibleAndClick(CatalogPage.delete_confirmation));
      test('should Verify that the modal "Deletion in progress" is well displayed', async () => {
        return promise
          .then(async () => await page.waitForSelector(CatalogPage.delete_modal + '.modal.show'))
          .then(async () => await page.waitForSelector(CatalogPage.delete_modal + '.modal:not(.show)'))
          .then(async () => await page.waitForSelector(CatalogPage.green_validation,{visible:'true'}));
      });
      test('should check that the duplicate products are deleted successfully', () => client.checkTextContent(CatalogPage.green_validation, 'Product(s) successfully deleted.'));
      test('should click on "Reset" button', async () => {
        await client.waitForVisibleAndClick(CatalogPage.reset_button);
        await page.waitForNavigation();
      });
      scenario('Check that the duplicate product has been deleted', client => {
        test('should set the search input to "copy" to search for the duplicated products', async () => await client.waitAndSetValue(CatalogPage.name_search_input, "copy"));
        test('should click on the "ENTER" key', async () => {
          await client.keys('Enter');
          await page.waitForNavigation();
        });
        test('should get a message indicates that no result found', () => client.checkTextValue(CatalogPage.search_result_message, 'There is no result for this search', "contain", 2000));
        test('should click on "Reset" button', () => client.waitForVisibleAndClick(CatalogPage.reset_button));
      }, 'catalogbulkaction');
    }, 'catalogbulkaction');

    scenario('Check existence product in Front Office', client => {
      test('should go to the "Front Office"', () => client.switchWindow(1));
      test('should search the deleted product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, 'copy'));
      test('should check the not existence of the disabled product', () => page.waitForSelector(SearchProductPage.empty_result_section));
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'catalogbulkaction');
  }, 'catalogbulkaction');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'product/product');

}, 'catalogbulkaction', true);
