/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import createOrderMap from './create-order-map';
import CreateOrderPage from "./create-order-page";

const $ = window.$;

/**
 * Responsible for summary block rendering
 */
export default class SummaryRenderer {
  constructor() {
    this.$totalProducts = $(createOrderMap.summaryTotalProducts);
    this.$totalDiscount = $(createOrderMap.summaryTotalDiscount);
    this.$totalShipping = $(createOrderMap.totalShippingField);
    this.$summaryTotalShipping = $(createOrderMap.summaryTotalShipping);
    this.$totalTaxes = $(createOrderMap.summaryTotalTaxes);
    this.$totalWithoutTax = $(createOrderMap.summaryTotalWithoutTax);
    this.$totalWithTax = $(createOrderMap.summaryTotalWithTax);
    this.$placeOrderCartIdField = $(createOrderMap.placeOrderCartIdField);
    this.$orderMessageField = $(createOrderMap.orderMessageField);
    this.$processOrderLink = $(createOrderMap.processOrderLinkTag);
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
    const addressesAreValid = CreateOrderPage.validateSelectedAddresses(cartInfo.addresses);

    if (noProducts || noShippingOptions || !addressesAreValid) {
      this._hideSummaryBlock();

      return;
    }
    const cartSummary = cartInfo.summary;
    this.$totalProducts.text(cartSummary.totalProductsPrice);
    this.$totalDiscount.text(cartSummary.totalDiscount);
    this.$summaryTotalShipping.text(cartSummary.totalShippingWithoutTaxes);
    this.$totalShipping.text(cartSummary.totalShippingPrice);
    this.$totalTaxes.text(cartSummary.totalTaxes);
    this.$totalWithoutTax.text(cartSummary.totalPriceWithoutTaxes);
    this.$totalWithTax.text(cartSummary.totalPriceWithTaxes);
    this.$processOrderLink.prop('href', cartSummary.processOrderLink);
    this.$orderMessageField.text(cartSummary.orderMessage);
    this.$placeOrderCartIdField.val(cartInfo.cartId);

    this._showSummaryBlock();
  }

  /**
   * Renders summary success message
   *
   * @param message
   */
  renderSuccessMessage(message) {
    $(createOrderMap.summarySuccessAlertText).text(message);
    this._showSummarySuccessAlertBlock();
  }

  /**
   * Renders summary error message
   *
   * @param message
   */
  renderErrorMessage(message) {
    $(createOrderMap.summaryErrorAlertText).text(message);
    this._showSummaryErrorAlertBlock();
  }

  /**
   * Cleans content of success/error summary alerts and hides them
   */
  cleanAlerts() {
    $(createOrderMap.summarySuccessAlertText).text('');
    $(createOrderMap.summaryErrorAlertText).text('');
    this._hideSummarySuccessAlertBlock();
    this._hideSummaryErrorAlertBlock();
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
   * Shows error alert of summary block
   *
   * @private
   */
  _showSummaryErrorAlertBlock() {
    $(createOrderMap.summaryErrorAlertBlock).removeClass('d-none');
  }

  /**
   * Hides error alert of summary block
   *
   * @private
   */
  _hideSummaryErrorAlertBlock() {
    $(createOrderMap.summaryErrorAlertBlock).addClass('d-none');
  }

  /**
   * Shows success alert of summary block
   *
   * @private
   */
  _showSummarySuccessAlertBlock() {
    $(createOrderMap.summarySuccessAlertBlock).removeClass('d-none');
  }

  /**
   * Hides success alert of summary block
   *
   * @private
   */
  _hideSummarySuccessAlertBlock() {
    $(createOrderMap.summarySuccessAlertBlock).addClass('d-none');
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
    this.$processOrderLink.prop('href', '');
    this.$orderMessageField.text('');
    this.cleanAlerts();
  }
}
