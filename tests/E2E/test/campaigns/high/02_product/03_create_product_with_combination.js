const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const commonScenarios = require('../../common_scenarios/product');
const combination = require('../../common_scenarios/combination');
let data = require('./../../../datas/product-data');
let promise = Promise.resolve();
global.productVariations = [];

scenario('Create product with combination in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
  test('should click on "NEW PRODUCT" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));

  scenario('Edit Basic settings', client => {
    test('should set the "product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, data.standard.name + 'C' + date_time));
    test('should set the "Summary" text', () => client.setEditorText(AddProductPage.summary_textarea, data.common.summary));
    test('should click on "Description" tab', () => client.waitForExistAndClick(AddProductPage.tab_description));
    test('should set the "Description" text', () => client.setEditorText(AddProductPage.description_textarea, data.common.description));
    test('should select the "Pack of products"', () => client.waitForExistAndClick(AddProductPage.product_combinations));
    test('should set the "product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, data.standard.name + 'C' + date_time));
    test('should select the "Product with combination" option', () => client.waitForExistAndClick(AddProductPage.product_combinations));
    test('should upload the first product picture', () => client.uploadPicture('1.png', AddProductPage.picture));
    test('should upload the second product picture', () => client.uploadPicture('2.jpg', AddProductPage.picture));
    test('should click on "CREATE A CATEGORY"', () => client.scrollWaitForExistAndClick(AddProductPage.product_create_category_btn, 50));
    test('should set the "New category name"', () => client.waitAndSetValue(AddProductPage.product_category_name_input, data.standard.new_category_name + 'C' + date_time));
    test('should click on "Create"', () => client.createCategory());
    test('should choose the created category as default', () => {
      return promise
        .then(() => client.waitForVisible(AddProductPage.created_category))
        .then(() => client.waitForExistAndClick(AddProductPage.home_delete_button));
    });
    test('should click on "ADD A BRAND" button', () => client.scrollWaitForExistAndClick(AddProductPage.product_add_brand_btn, 50));
    test('should select brand', () => {
      return promise
        .then(() => client.waitForExistAndClick(AddProductPage.product_brand_select))
        .then(() => client.waitForExistAndClick(AddProductPage.product_brand_select_option));
    });
    test('should click on "ADD RELATED PRODUCT" button', () => client.waitForExistAndClick(AddProductPage.add_related_product_btn));
    test('should search and add a related product', () => client.searchAndAddRelatedProduct());
    commonScenarios.addProductFeature(client, "Frame Size", 0, "Cotton");
    commonScenarios.addProductFeature(client, "Compositions", 1, '', "Azerty", "custom_value");
    test('should set "Tax exclude" price', () => client.setPrice(AddProductPage.priceTE_shortcut, data.common.priceTE));
    test('should set the "Reference" input', () => client.waitAndSetValue(AddProductPage.product_reference, data.common.product_reference));
    test('should switch the product online', () => {
      return promise
        .then(() => client.isVisible(AddProductPage.symfony_toolbar))
        .then(() => {
          if (global.isVisible) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar);
          }
        })
        .then(() => client.waitForExistAndClick(AddProductPage.product_online_toggle, 3000));
    });
  }, 'product/product');

  /**
   * This scenario is based on the bug described in this ticket
   * http://forge.prestashop.com/browse/BOOM-3165
   * http://forge.prestashop.com/browse/BOOM-4469
   **/
  scenario('Create product with combinations', client => {
    test('should click on "Combinations" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_combinations_tab, 50));
    combination.createCombinations(client);
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
  }, 'product/create_combinations');

  scenario('Edit the created combinations', client => {
    test('should get the number of combinations', () => {
      return promise
        .then(() => client.pause(9000))
        .then(() => client.getTextInVar(AddProductPage.combination_total_number, "combinationsNumber"))
        .then(() => {
          combination.editCombinations(global.tab["combinationsNumber"]);
        })
        .then(() => {
          combination.editShipping();
        })
        .then(() => {
          combination.editPricing();
        })
        .then(() => {
          combination.editSeoInformations();
        })
        .then(() => {
          combination.editProductOptions();
        })
        .then(() => {
          scenario('Save Product', client => {
            test('should click on "SAVE" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
            test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
            test('should logout successfully from the Back Office', () => client.signOutBO());
          }, 'product/product');
        })
        .then(() => {
          combination.checkProductCreationBO(AccessPageBO, global.tab["combinationsNumber"]);
        })
        .then(() => {
          scenario('Check the product with combination in the Front Office', () => {
            scenario('Login in the Front Office', client => {
              test('should open the browser', () => client.open());
              test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
              combination.checkCombinationProductFo(SearchProductPage, productPage, AccessPageFO);
            }, 'product/product');
          }, 'product/product');
        });
    });
  }, 'product/create_combinations');
}, 'product/product', true);
