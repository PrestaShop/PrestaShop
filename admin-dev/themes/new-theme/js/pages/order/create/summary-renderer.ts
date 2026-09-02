/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
