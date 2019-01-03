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
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const common_scenarios = require('../../common_scenarios/product');
let promise = Promise.resolve();

scenario('Check the sort of products in the Back Office', client => {
  test('should open browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog" page', () => {
    return promise
      .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu))
      .then(() => client.getProductPageNumber('product_catalog_list'));
  });

  common_scenarios.sortProduct(ProductList.product_id, 'id_product');
  common_scenarios.sortProduct(ProductList.product_name, 'name');
  common_scenarios.sortProduct(ProductList.product_reference, 'reference');

  scenario('Back to the default sort', client => {
    test('should click on "Sort by DESC" icon By ID', () => {
      return promise
        .then(() => client.pause(7000))
        .then(() => client.moveToObject(ProductList.sort_button.replace('%B', 'id_product')))
        .then(() => client.waitForExistAndClick(ProductList.sort_button.replace('%B', 'id_product')))
        .then(() => client.waitForExistAndClick(ProductList.sort_button.replace('%B', 'id_product')));
    });
  }, 'product/product');
}, 'product/product', true);
