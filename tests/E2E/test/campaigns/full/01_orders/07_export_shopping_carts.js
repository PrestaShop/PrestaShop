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
const {Menu} = require('../../../selectors/BO/menu.js');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {ShoppingCart} = require('../../../selectors/BO/order');
const orderCommonScenarios = require('../../common_scenarios/order');

scenario('Export shopping carts in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'order');
  scenario('Search for the shopping carts to export', client => {
    test('should go to "Shopping cart" page', () => client.goToSubtabMenuPage(Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.shopping_carts_submenu));
    test('should set the "Customer" input', () => client.waitAndSetValue(ShoppingCart.search_input.replace('%searchParam', 'c!lastname'), 'DOE'));
    test('should set the "Carrier" input', () => client.waitAndSetValue(ShoppingCart.search_input.replace('%searchParam', 'ca!name'), 'My carrier'));
    test('should click on the "search" button', () => client.waitForExistAndClick(ShoppingCart.search_button));
    test('should get the "Shopping Carts" number', () => client.getShoppingCartNumber(ShoppingCart.id_shopping_carts));
    test('should get the "Shopping Carts" info', () => orderCommonScenarios.getShoppingCartsInfo(client));
    test('should export the "Shopping Carts" then check the exported file information', () => orderCommonScenarios.checkExportedFile(client));
  }, 'order');
}, 'order', true);
