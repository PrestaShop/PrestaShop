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
const Product = require('./product');
const {AddProductPage} = require('../../selectors/BO/add_product_page');

class CheckProductBO extends Product {

  searchProductByName(productName) {
    return this.client
      .waitForExistAndClick(AddProductPage.catalogue_filter_by_name_input)
      .waitAndSetValue(AddProductPage.catalogue_filter_by_name_input, productName)
      .waitForExistAndClick(AddProductPage.catalogue_submit_filter_button);
  }
  checkProductPriceTE(priceTE) {
    return this.client
      .waitForExist(AddProductPage.catalog_product_price, 60000)
      .then(() => this.client.getText(AddProductPage.catalog_product_price))
      .then((price) => expect(price.replace('€', '')).to.be.equal(priceTE + '.00'));
  }
}

module.exports = CheckProductBO;
