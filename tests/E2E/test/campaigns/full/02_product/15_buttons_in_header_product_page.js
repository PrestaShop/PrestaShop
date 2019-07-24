/**
 * This script is based on the scenario described in this test link
 * [id="PS-390"][Name="Buttons in header product page"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const commonScenarios = require('../../common_scenarios/product');
let promise = Promise.resolve();
const welcomeScenarios = require('../../common_scenarios/welcome');

let productData = {
  name: 'PH',
  quantity: "10",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'test_header_button',
  type: 'combination',
  attribute: {
    1: {
      name: 'color',
      variation_quantity: '10'
    }
  }
};

scenario('Check that the buttons in header product page works successfully', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');
  welcomeScenarios.findAndCloseWelcomeModal();
  commonScenarios.createProduct(AddProductPage, productData);

  scenario('Check that "Type of product" select works successfully', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should search for the product with combination "' + productData.name + date_time + '" by name', () => client.searchProductByName(productData.name + date_time));
    test('should click on "Edit" button', () => client.waitForExistAndClick(ProductList.edit_button));
    test('should check that "Combinations" block exist', () => client.isExisting(AddProductPage.product_combinations.replace('%I', 2)));
    test('should check that the "Combinations" tab does exist', () => client.isExisting(AddProductPage.product_combinations_tab));
    test('should click on "Simple product" radio button', () => client.waitForExistAndClick(AddProductPage.product_combinations.replace('%I', 1)));
    test('should verify the appearance of the warning modal', () => client.checkTextValue(AddProductPage.confirmation_modal_content, 'This will delete all the combinations. Do you wish to proceed?', 'equal', 3000));
    test('should click on "Yes" button from the modal', () => {
      return promise
        .then(() => client.waitForExistAndClick(AddProductPage.delete_confirmation_button.replace('%BUTTON', 'Yes')))
        .then(() => client.refresh());
    });
    test('should change the product type to "Standard"', () => client.waitAndSelectByValue(AddProductPage.product_type, 0));
    test('should check that the "Show variation" does exist', () => client.isExisting(AddProductPage.product_combinations.replace('%I', 1)));
    test('should change the product type to "Pack"', () => client.waitAndSelectByValue(AddProductPage.product_type, 1));
    test('should check that the "Input pack items" does exist', () => client.isExisting(AddProductPage.input_pack_item, 2000));
    test('should change the product type to "Virtual"', () => client.waitAndSelectByValue(AddProductPage.product_type, 2));
    test('should check that the "Virtual product" tab does exist', () => client.isExisting(AddProductPage.product_quantities_tab));
    test('should click on "Virtual product" tab', () => client.waitForExistAndClick(AddProductPage.product_quantities_tab, 3000));
    test('should check that "Does this product have an associated file?" question exists', () => client.isExisting(AddProductPage.virtual_associated_file.replace('%ID', '0')));
  }, 'product/check_product');

  scenario('Check that "Language" button works successfully in the Back Office and the Front Office', client => {
    test('should select the "French" language from the list', () => client.waitAndSelectByValue(AddProductPage.product_language, 'fr'));
    test('should check that the "Product name" input exists', () => client.isExisting(AddProductPage.product_name_fr_input, 2000));
    test('should set the "Product name" input', () => client.waitAndSetValue(AddProductPage.product_name_fr_input, 'produit' + date_time));
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 2000));
    commonScenarios.clickOnPreviewLink(client, AddProductPage.preview_link, productPage.product_name);
    test('should go back to the Back Office', () => client.switchWindow(0));
  }, 'product/product');

  scenario('Check that "Sales" button works successfully', client => {
    test('should check that "Product details" block is displayed in stats page', () => {
      return promise
        .then(() => client.switchWindow(2))
        .then(() => client.isExisting(AddProductPage.calendar_form, 2000)) //Calendar in stats page
        .then(() => client.switchWindow(0));
    });
  }, 'product/product');

  scenario('Save the created product', client => {
    test('should go to "Catalog" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
  }, 'product/product');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'product/product');

}, 'product/product', true);
