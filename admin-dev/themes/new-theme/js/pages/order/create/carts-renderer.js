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

import createOrderPageMap from './create-order-map';

const $ = window.$;

/**
 * Renders customer carts list
 */
export default class CartsRenderer {

  /**
   * Renders customer carts from checkout history
   *
   * @param {Array} carts
   * @param {Int} currentCartId
   */
  render({carts, currentCartId}) {
    const $cartsTable = $(createOrderPageMap.customerCartsTable);
    const $cartsTableRowTemplate = $($(createOrderPageMap.customerCartsTableRowTemplate).html());

    $cartsTable.find('tbody').empty();

    if (!carts) {
      return;
    }

    this._showCheckoutHistoryBlock();

    for (const key in carts) {
      const cart = carts[key];
      // do not render current cart
      if (cart.cartId === currentCartId) {
        continue;
      }
      const $template = $cartsTableRowTemplate.clone();

      $template.find('.js-cart-id').text(cart.cartId);
      $template.find('.js-cart-date').text(cart.creationDate);
      $template.find('.js-cart-total').text(cart.totalPrice);

      $template.find('.js-use-cart-btn').data('cart-id', cart.cartId);

      $cartsTable.find('tbody').append($template);
    }
  }

  /**
   * Shows checkout history block where carts and orders are rendered
   *
   * @private
   */
  _showCheckoutHistoryBlock() {
    $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
  }
}
