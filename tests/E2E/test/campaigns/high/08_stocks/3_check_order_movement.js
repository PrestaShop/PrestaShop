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
const {Movement} = require('../../../selectors/BO/catalogpage/stocksubmenu/movements');
const {OrderPage} = require('../../../selectors/BO/order');
const {CreateOrder} = require('../../../selectors/BO/order');
const orderScenarios = require('../../common_scenarios/order');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const stockCommonScenarios = require('../../common_scenarios/stock');

const common_scenarios = require('../../common_scenarios/product');

let productData = {
  name: 'Mvt',
  quantity: "4",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'mvt',
  type: 'combination',
  attribute: {
    1: {
      name: 'color',
      variation_quantity: '4'
    }
  }
};

scenario('Check order movement', client => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'stocks');

  scenario('Create "Product"', () => {
    common_scenarios.createProduct(AddProductPage, productData);
  }, 'order');

  orderScenarios.createOrderBO(OrderPage, CreateOrder, productData);

  scenario('Change order state to "Delivered"', client => {
    test('should go to "Orders" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu));
    test('should go to the first order', () => client.waitForExistAndClick(OrderPage.first_order));
    test('should change order state to "Delivered"', () => client.changeOrderState(OrderPage, 'Delivered'));
    test('should get the order quantity', () => client.getTextInVar(OrderPage.order_quantity.replace("%NUMBER", 1), "orderQuantity"));
  }, 'stocks');

  scenario('Check order movement', client => {
    test('should go to "Stocks" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu));
    stockCommonScenarios.checkMovementHistory(client, Menu, Movement, 1, "4", "-",  "Customer Order", "mvt");
  }, 'stocks');

}, 'stocks', true);
