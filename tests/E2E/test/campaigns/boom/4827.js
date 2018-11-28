const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../selectors/BO/access_page');
const {Menu} = require('../../selectors/BO/menu.js');
let data = require('./../../datas/product-data');
let promise = Promise.resolve();
global.productVariations = [];

scenario('BOOM-4827: Create product with combination in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Edit Basic settings', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should click on "NEW PRODUCT" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));
    test('should set the "product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, data.standard.name + 'C' + date_time));
    test('should set the "Summary" text', () => client.setEditorText(AddProductPage.summary_textarea, data.common.summary));
    test('should click on "Description" tab', () => client.waitForExistAndClick(AddProductPage.tab_description));
    test('should set the "Description" text', () => client.setEditorText(AddProductPage.description_textarea, data.common.description));
    test('should select the "Product with combinations"', () => client.waitForExistAndClick(AddProductPage.product_combinations));
    test('should upload the first product picture', () => client.uploadPicture('1.png', AddProductPage.picture));
    test('should upload the second product picture', () => client.uploadPicture('2.jpg', AddProductPage.picture));
  }, 'common_client');

  scenario('Create product combinations', client => {
    test('should click on "Combinations" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_combinations_tab, 50));
    test('should choose the size "S" and color "Grey"', () => client.createCombination(AddProductPage.combination_size_s, AddProductPage.combination_color_grey));
    test('should choose the size "M" and color "Beige"', () => client.createCombination(AddProductPage.combination_size_m, AddProductPage.combination_color_beige));
    test('should click on "Generate" button', () => client.waitForExistAndClick(AddProductPage.combination_generate_button));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should click on "Edit" second combination', () => {
      return promise
        .then(() => client.getCombinationData(2, 3000))
        .then(() => client.goToEditCombination());
    });
    test('should edit second combination', () => client.editCombination(2));
    test('should click on "Set as default combination" button', () => client.scrollWaitForExistAndClick(AddProductPage.default_combination.replace('%NUMBER', combinationId)));
    test('should go back to combination list', () => client.backToProduct());
    test('should check that the second combination is the default one', () => client.isSelected(AddProductPage.combination_default_button.replace('%NUMBER', combinationId)));
  }, 'product/create_combinations');
}, 'common_client', true);

