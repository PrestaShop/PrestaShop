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

/**
 * Responsible for summary block rendering
 */
export default class SummaryRenderer {
  constructor() {
    this.$totalProducts = $(createOrderMap.summaryTotalProducts);
    this.$totalDiscount = $(createOrderMap.summaryTotalDiscount);
    this.$totalShipping = $(createOrderMap.totalShippingField);
    this.$totalTaxes = $(createOrderMap.summaryTotalTaxes);
    this.$totalWithoutTax = $(createOrderMap.summaryTotalWithoutTax);
    this.$totalWithTax = $(createOrderMap.summaryTotalWithTax);
    this.$placeOrderCartIdField = $(createOrderMap.placeOrderCartIdField);
  }

  /**
   * Renders summary block
   *
   * @param {Object} cartInfo
   */
  render(cartInfo) {
    this._cleanSummary();
    const noProducts = cartInfo.products.length === 0;
    const noShippingOptions = cartInfo.shipping === null;
    const invalidAddresses = !this._addressesAreValid(cartInfo.addresses);

    if (noProducts || noShippingOptions || invalidAddresses) {
      this._hideSummaryBlock();

      return;
    }
    const cartSummary = cartInfo.summary;

    this.$totalProducts.text(cartSummary.totalProductsPrice);
    this.$totalDiscount.text(cartSummary.totalDiscount);
    this.$totalShipping.text(cartSummary.totalShippingPrice);
    this.$totalTaxes.text(cartSummary.totalTaxes);
    this.$totalWithoutTax.text(cartSummary.totalPriceWithTaxes);
    this.$totalWithTax.text(cartSummary.totalPriceWithoutTaxes);
    this.$placeOrderCartIdField.val(cartInfo.cartId);

    this._showSummaryBlock();
  }

  /**
   * Validates that cart has valid addresses
   *@todo refactor and show addresses warning to create new address if empty
   * @param addresses
   *
   * @returns {boolean}
   *
   * @private
   */
  _addressesAreValid(addresses) {
    if (addresses.length !== 0) {
      return false;
    }

    for (const key in addresses) {
      const address = addresses[key];

      if (address.countryIsEnabled) {
        if (address.invoice || address.delivery) {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * Shows summary block
   *
   * @private
   */
  _showSummaryBlock() {
    $(createOrderMap.summaryBlock).removeClass('d-none');
  }

  /**
   * Hides summary block
   *
   * @private
   */
  _hideSummaryBlock() {
    $(createOrderMap.summaryBlock).addClass('d-none');
  }

  /**
   * Empties cart summary fields
   */
  _cleanSummary() {
    this.$totalProducts.empty();
    this.$totalDiscount.empty();
    this.$totalShipping.empty();
    this.$totalTaxes.empty();
    this.$totalWithoutTax.empty();
    this.$totalWithTax.empty();
  }
}
