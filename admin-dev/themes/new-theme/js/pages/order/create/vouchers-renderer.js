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
 * Renders cart vouchers block
 */
export default class VouchersRenderer {

  /**
   * Renders cart vouchers (a.k.a cart rules/discounts)
   *
   * @param {Array} vouchers
   */
  render(vouchers) {
    const $vouchersTable = $(createOrderPageMap.vouchersTable);
    const $vouchersTableRowTemplate = $($(createOrderPageMap.vouchersTableRowTemplate).html());

    $vouchersTable.find('tbody').empty();

    if (vouchers.length === 0) {
      $vouchersTable.addClass('d-none');

      return;
    }

    $vouchersTable.removeClass('d-none');

    for (const key in vouchers) {
      const voucher = vouchers[key];

      const $template = $vouchersTableRowTemplate.clone();

      $template.find('.js-voucher-name').text(voucher.name);
      $template.find('.js-voucher-description').text(voucher.description);
      $template.find('.js-voucher-value').text(voucher.value);

      $template.find('.js-voucher-delete-btn').data('cart-rule-id', voucher.cartRuleId);

      $vouchersTable.find('tbody').append($template);
    }

    this._showVouchersBlock();
  }

  /**
   * Shows vouchers block
   *
   * @private
   */
  _showVouchersBlock() {
    $(createOrderPageMap.vouchersBlock).removeClass('d-none');
  }
}
