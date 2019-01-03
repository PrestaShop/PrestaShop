/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
const common_scenarios = require('../common_scenarios/product');
const welcomeScenarios = require('../common_scenarios/welcome');
const {AccessPageBO} = require('../../selectors/BO/access_page');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {productPage} = require('../../selectors/FO/product_page');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const {SearchProductPage} = require('../../selectors/FO/search_product_page');

let promise = Promise.resolve();

let productData = {
  name: 'Dress',
  quantity: "10",
  price: '5',
  image_name: '1.png',
  reference: 'robe'
};

scenario('Create "Product"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  common_scenarios.createProduct(AddProductPage, productData);
  common_scenarios.checkProductBO(AddProductPage, productData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);

scenario('Check the created product in the Front Office', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'product/product');
  scenario('Check that the created product is well displayed in the Front Office', client => {
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productData.name + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should check that the product name is equal to "' + (productData.name + date_time).toUpperCase() + '"', () => client.checkTextValue(productPage.product_name, (productData.name + date_time).toUpperCase()));
    test('should check that the product price is equal to "€6.00"', () => client.checkTextValue(productPage.product_price, '€6.00'));
    test('should check that the product reference is equal to "' + productData.reference + '"', () => {
      return promise
        .then(() => client.scrollTo(productPage.product_reference))
        .then(() => client.checkTextValue(productPage.product_reference, productData.reference))
    });
    test('should check that the product quantity is equal to "10"', () => client.checkAttributeValue(productPage.product_quantity, 'data-stock', productData.quantity));
  }, 'product/product');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => {
      return promise
        .then(() => client.scrollTo(AccessPageFO.sign_out_button))
        .then(() => client.signOutFO(AccessPageFO))
    });
  }, 'product/product');
}, 'product/product', true);
