const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../selectors/BO/access_page');
const {Menu} = require('../../selectors/BO/menu.js');
let data = require('./../../datas/product-data');
let promise = Promise.resolve();
global.productVariations = [];

scenario('BOOM-3704: Create product with combination in the Back Office', () => {
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
    test('should set the "Impact on price" to "2,5"', () => {
      return promise
        .then(() => client.getCombinationData(2))
        .then(() => client.showElement("td.attribute-price", 1))
        .then(() => client.waitAndSetValue(AddProductPage.combination_impact_price_input.replace('%NUMBER', global.combinationId), '2,5'));
    });
    test('should click on "Basic settings" tab', () => client.scrollWaitForExistAndClick(AddProductPage.basic_settings_tab, 50));
    test('should set the "Tax exclude" price', () => {
      return promise
        .then(() => client.scrollTo(AddProductPage.priceTE_shortcut, 50))
        .then(() => client.waitAndSetValue(AddProductPage.priceTE_shortcut, data.common.priceTE));
    });
    test('should click on "Combinations" tab', () => client.scrollWaitForExistAndClick(AddProductPage.product_combinations_tab, 50));
    test('should check that the final price is equal to "26.666666 €"', () => {
      return promise
        .then(() => client.showElement("td.attribute-finalprice", 1))
        .then(() => client.checkTextValue(AddProductPage.combination_final_price_span.replace('%NUMBER', global.combinationId), "26.666666"));
    });
  }, 'product/create_combinations');
}, 'common_client', true);

