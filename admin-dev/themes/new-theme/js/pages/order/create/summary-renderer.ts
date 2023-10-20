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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import createOrderMap from './create-order-map';
import {ValidateAddresses} from './address-validator';

const {$} = window;

/**
 * Responsible for summary block rendering
 */
export default class SummaryRenderer {
  $totalProducts: JQuery;

  $totalDiscount: JQuery;

  $totalShipping: JQuery;

  $summaryTotalShipping: JQuery;

  $totalTaxes: JQuery;

  $totalWithoutTax: JQuery;

  $totalWithTax: JQuery;

  $placeOrderCartIdField: JQuery;

  $orderMessageField: JQuery;

  $processOrderLink: JQuery;

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
  render(cartInfo: Record<string, any>): void {
    this.cleanSummary();
    const noProducts = cartInfo.products.length === 0;
    const noShippingOptions = cartInfo.shipping === null;
    const addressesAreValid = ValidateAddresses(cartInfo.addresses);

    if (noProducts || noShippingOptions || !addressesAreValid) {
      this.hideSummaryBlock();

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

    this.showSummaryBlock();
  }

  /**
   * Renders summary success message
   *
   * @param message
   */
  renderSuccessMessage(message: string): void {
    $(createOrderMap.summarySuccessAlertText).text(message);
    this.showSummarySuccessAlertBlock();
  }

  /**
   * Renders summary error message
   *
   * @param message
   */
  renderErrorMessage(message: string): void {
    $(createOrderMap.summaryErrorAlertText).text(message);
    this.showSummaryErrorAlertBlock();
  }

  /**
   * Cleans content of success/error summary alerts and hides them
   */
  cleanAlerts(): void {
    $(createOrderMap.summarySuccessAlertText).text('');
    $(createOrderMap.summaryErrorAlertText).text('');
    this.hideSummarySuccessAlertBlock();
    this.hideSummaryErrorAlertBlock();
  }

  /**
   * Shows summary block
   *
   * @private
   */
  private showSummaryBlock(): void {
    $(createOrderMap.summaryBlock).removeClass('d-none');
  }

  /**
   * Hides summary block
   *
   * @private
   */
  private hideSummaryBlock(): void {
    $(createOrderMap.summaryBlock).addClass('d-none');
  }

  /**
   * Shows error alert of summary block
   *
   * @private
   */
  private showSummaryErrorAlertBlock(): void {
    $(createOrderMap.summaryErrorAlertBlock).removeClass('d-none');
  }

  /**
   * Hides error alert of summary block
   *
   * @private
   */
  private hideSummaryErrorAlertBlock(): void {
    $(createOrderMap.summaryErrorAlertBlock).addClass('d-none');
  }

  /**
   * Shows success alert of summary block
   *
   * @private
   */
  private showSummarySuccessAlertBlock(): void {
    $(createOrderMap.summarySuccessAlertBlock).removeClass('d-none');
  }

  /**
   * Hides success alert of summary block
   *
   * @private
   */
  private hideSummarySuccessAlertBlock(): void {
    $(createOrderMap.summarySuccessAlertBlock).addClass('d-none');
  }

  /**
   * Empties cart summary fields
   */
  cleanSummary(): void {
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
