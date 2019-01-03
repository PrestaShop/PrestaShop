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
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {productPage} = require('../../../selectors/FO/product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
let promise = Promise.resolve();

scenario('Check that all products are well displayed in the Back Office', client => {
  test('should open the browser', () => client.open());
  test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  test('should go to "Catalog"', () => {
    return promise
      .then(() => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu))
      .then(() => client.waitAndSelectByValue(ProductList.status_select, '1'))
      .then(() => client.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button))
      .then(() => client.isVisible(ProductList.pagination_products))
      .then(() => client.getProductsNumber(ProductList.pagination_products))
      .then(() => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
  });
  test('should access to the Front Office', () => client.accessToFO(AccessPageFO));
  test('should set the language of shop to "English"', () => client.changeLanguage());
  test('should click on "SEE ALL PRODUCTS" link', () => client.scrollWaitForExistAndClick(productPage.see_all_products));
  test('should check the number of displayed products', () => client.checkTextValue(productPage.products_number, productsNumber, 'contain'));
  test('should check the existence of pagination', () => {
    return promise
      .then(() => client.isVisible(productPage.pagination_next))
      .then(() => client.clickNextOrPrevious(productPage.pagination_next));
  });
  test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product_all));
}, 'product/product', true);
