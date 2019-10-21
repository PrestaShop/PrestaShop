/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import createOrderMap from './create-order-map';

const $ = window.$;

export default class ProductsListRenderer {
  constructor() {
    this.$productsTable = $(createOrderMap.productsTable);
  }
  render(products) {
    if (products.length === 0) {
      this._hideProductsList();

      return;
    }
    this._renderList(products);
  }

  _renderList(products) {
    this._cleanProductsList();
    const $productsTableRowTemplate = $($(createOrderMap.productsTableRowTemplate).html());

    for (const key in products) {
      const product = products[key];
      const $template = $productsTableRowTemplate.clone();

      $template.find(createOrderMap.productImageField).text(product.imageLink);
      $template.find(createOrderMap.productNameField).text(product.name);
      $template.find(createOrderMap.productAttrField).text(product.attribute);
      $template.find(createOrderMap.productReferenceField).text(product.reference);
      $template.find(createOrderMap.productUnitPriceInput).text(product.unitPrice);
      $template.find(createOrderMap.productTotalPriceField).text(product.price);
      $template.find(createOrderMap.productRemoveBtn).data('product-id', product.productId);

      this.$productsTable.find('tbody').append($template);
    }

    this._showProductsList();
  }

  /**
   * Shows products list
   *
   * @private
   */
  _showProductsList() {
    this.$productsTable.removeClass('d-none');
  }

  /**
   * Hides products list
   *
   * @private
   */
  _hideProductsList() {
    this.$productsTable.addClass('d-none');
  }

  /**
   * Emptes products list
   *
   * @private
   */
  _cleanProductsList() {
    this.$productsTable.find('tbody').empty();
  }
}
