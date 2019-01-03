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
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Stock} = require('../../../selectors/BO/catalogpage/stocksubmenu/stock');
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const common_scenarios = require('../../common_scenarios/product');
const {Menu} = require('../../../selectors/BO/menu.js');
const welcomeScenarios = require('../../common_scenarios/welcome');
let promise = Promise.resolve();

let productData = [{
  name: 'FirstProduct',
  reference: 'firstProduct',
  quantity: "100",
  price: '5',
  image_name: 'image_test.jpg'
}, {
  name: 'SecondProduct',
  reference: 'secondProduct',
  quantity: "100",
  price: '5',
  image_name: 'image_test.jpg'
}];

scenario('Modify quantity and check the movement of a group of product', client => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');
  welcomeScenarios.findAndCloseWelcomeModal();
  common_scenarios.createProduct(AddProductPage, productData[0]);
  common_scenarios.createProduct(AddProductPage, productData[1]);

  scenario('Modify quantity and check the movement of a group of product', client => {
    test('should go to "Stocks" page', () => {
      return promise

        .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu))
        .then(() => client.pause(5000))
        .then(() => client.isVisible(Stock.sort_product_icon, 2000))
        .then(() => client.pause(5000))
        .then(() => {
          if (global.isVisible) {
            client.waitForVisibleAndClick(Stock.sort_product_icon);
            client.pause(3000);
          }
        })
    });
    test('should set the "Quantity" of the first product to 15', () => client.modifyProductQuantity(Stock, 2, 15));
    test('should set the "Quantity" of the second product to 50', () => client.modifyProductQuantity(Stock, 1, 50));
    test('should click on "Apply new quantity" button', () => client.waitForExistAndClick(Stock.group_apply_button));
    test('should click on "Movements" tab', () => client.goToStockMovements(Menu, Movement));
    test('should verify the new "Quantity" and "Type" of the two changed products', () => {
      return promise
        .then(() => client.pause(2000))
        .then(() => client.checkOrderMovement(Movement, client));
    });
  }, 'stocks');
}, 'stocks', true);
