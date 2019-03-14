const {AccessPageBO} = require('../../selectors/BO/access_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const {productPage} = require('../../selectors/FO/product_page');
const {CheckoutOrderPage} = require('../../selectors/FO/order_page');
const commonScenarios = require('../common_scenarios/discount');

let catalogPriceRule = [{
  name: 'Catalog_price_1',
  type: "percentage",
  reduction: '18'
}, {
  name: 'Catalog_price_2',
  type: "percentage",
  reduction: '25',
  quantity: '48'
}];

scenario('BOOM-3843: Check double catalog price rules', () => {

  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  commonScenarios.createCatalogPriceRules(catalogPriceRule[0].name, catalogPriceRule[0].type, catalogPriceRule[0].reduction);
  commonScenarios.createCatalogPriceRules(catalogPriceRule[1].name, catalogPriceRule[1].type, catalogPriceRule[1].reduction, catalogPriceRule[1].quantity);

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');

  scenario('Check Catalog Price Rules in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product));
    test('should verify that the discount is equal to "18%"', () => client.checkTextValue(productPage.product_discount_details, catalogPriceRule[0].reduction, "contain"));
    test('should set quantity to "48"', () => client.waitAndSetValue(productPage.first_product_quantity, 48));
    test('should verify that the discount is equal to "25%"', () => client.checkTextValue(productPage.product_discount_details, catalogPriceRule[1].reduction, "contain", 2000));
    test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(CheckoutOrderPage.add_to_cart_button));
    test('should click on "Continue shopping" button', () => client.waitForExistAndClick(CheckoutOrderPage.continue_shopping_button, 2000));
    test('should verify that the discount is equal to "25%"', () => client.checkTextValue(productPage.product_discount_details, catalogPriceRule[1].reduction, "contain", 2000));
    test('should click on "CART" button', () => client.waitForExistAndClick(AccessPageFO.shopping_cart_button));
    test('should verify that the discount is equal to "25%"', () => client.checkTextValue(CheckoutOrderPage.cart_product_discount, catalogPriceRule[1].reduction, "contain"));
  }, 'common_client');

  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'common_client');

  scenario('Open the browser and connect to the BO', client => {
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  commonScenarios.deleteCatalogPriceRules(catalogPriceRule[0].name);
  commonScenarios.deleteCatalogPriceRules(catalogPriceRule[1].name);

  scenario('logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');

}, 'common_client', true);
