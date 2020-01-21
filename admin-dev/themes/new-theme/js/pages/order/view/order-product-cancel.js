/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import { NumberFormatter } from '@app/cldr';

const {$} = window;

/**
 * manages all product cancel actions, that includes all refund operations
 */
export default class OrderProductCancel {
  constructor() {
    this.router = new Router();
    this.cancelProductForm = $(OrderViewPageMap.cancelProduct.form);
    this.orderId = this.cancelProductForm.data('orderId');
    this.orderDelivered = parseInt(this.cancelProductForm.data('isDelivered'), 10) === 1;
    this.isTaxIncluded = parseInt(this.cancelProductForm.data('isTaxIncluded'), 10) === 1;
    this.discountsAmount = parseFloat(this.cancelProductForm.data('discountsAmount'));
    this.currencyFormatter = NumberFormatter.build(this.cancelProductForm.data('priceSpecification'));
    this.listenForInputs();
  }

  showPartialRefund() {
    // Always start by hiding elements then show the others, since some elements are common
    $(OrderViewPageMap.cancelProduct.toggle.standardRefund).hide();
    $(OrderViewPageMap.cancelProduct.toggle.partialRefund).show();
    $(OrderViewPageMap.cancelProduct.table.actions).hide();
    this.initForm(
      $(OrderViewPageMap.cancelProduct.buttons.save).data('partialRefundLabel'),
      this.router.generate('admin_orders_partial_refund', {orderId: this.orderId})
    );
  }

  showStandardRefund() {
    // Always start by hiding elements then show the others, since some elements are common
    $(OrderViewPageMap.cancelProduct.toggle.partialRefund).hide();
    $(OrderViewPageMap.cancelProduct.toggle.standardRefund).show();
    $(OrderViewPageMap.cancelProduct.table.actions).hide();
    this.initForm(
      $(OrderViewPageMap.cancelProduct.buttons.save).data('standardRefundLabel'),
      ''
    );
  }

  hideRefund() {
    $(OrderViewPageMap.cancelProduct.toggle.partialRefund).hide();
    $(OrderViewPageMap.cancelProduct.toggle.standardRefund).hide();
    $(OrderViewPageMap.cancelProduct.table.actions).show();
  }

  initForm(actionName, formAction) {
    this.updateVoucherRefund();

    this.cancelProductForm.prop('action', formAction);
    $(OrderViewPageMap.cancelProduct.buttons.save).html(actionName);
    $(OrderViewPageMap.cancelProduct.table.header).html(actionName);
    $(OrderViewPageMap.cancelProduct.checkboxes.restock).prop('checked', this.orderDelivered);
    $(OrderViewPageMap.cancelProduct.checkboxes.creditSlip).prop('checked', true);
  }

  listenForInputs() {
    $(document).on('change', OrderViewPageMap.cancelProduct.inputs.quantity, (event) => {
      const $productQuantityInput = $(event.target);
      const $parentCell = $productQuantityInput.parents(OrderViewPageMap.cancelProduct.table.cell);
      const $productAmount = $parentCell.find(OrderViewPageMap.cancelProduct.inputs.amount);
      const productQuantity = parseInt($productQuantityInput.val(), 10);
      if (productQuantity <= 0) {
        $productAmount.val(0);
        this.updateVoucherRefund();

        return;
      }
      const priceFieldName = this.isTaxIncluded ? 'productPriceTaxIncl' : 'productPriceTaxExcl';
      const productUnitPrice = parseFloat($productQuantityInput.data(priceFieldName));
      const amountRefundable = parseFloat($productQuantityInput.data('amountRefundable'));
      const guessedAmount = (productUnitPrice * productQuantity) < amountRefundable ?
        (productUnitPrice * productQuantity) : amountRefundable;
      const amountValue = parseFloat($productAmount.val());
      if ($productAmount.val() === '' || amountValue === 0 || amountValue > guessedAmount) {
        $productAmount.val(guessedAmount);
        this.updateVoucherRefund();
      }
    });

    $(document).on('change', OrderViewPageMap.cancelProduct.inputs.amount, () => {
      this.updateVoucherRefund();
    });
  }

  updateVoucherRefund() {
    let totalAmount = 0;
    $(OrderViewPageMap.cancelProduct.inputs.amount).each((index, amount) => {
      const floatValue = parseFloat(amount.value);
      totalAmount += !Number.isNaN(floatValue) ? floatValue : 0;
    });

    this.updateVoucherRefundTypeLabel(
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.productPrices),
      totalAmount
    );
    const refundVoucherExcluded = totalAmount - this.discountsAmount;
    this.updateVoucherRefundTypeLabel(
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.productPricesVoucherExcluded),
      refundVoucherExcluded
    );

    // Disable voucher excluded option when the voucher amount is too high
    if (refundVoucherExcluded < 0) {
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.productPricesVoucherExcluded)
        .prop('checked', false)
        .prop('disabled', true);
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.productPrices).prop('checked', true);
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.negativeErrorMessage).show();
    } else {
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.productPricesVoucherExcluded).prop('disabled', false);
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.negativeErrorMessage).hide();
    }
  }

  updateVoucherRefundTypeLabel($input, refundAmount) {
    const defaultLabel = $input.data('defaultLabel');
    const $label = $input.parents('label');
    const formattedAmount = this.currencyFormatter.format(refundAmount);

    // Change the ending text part only to avoid removing the input (the EOL is on purpose for better display)
    $label.get(0).lastChild.nodeValue = `
    ${defaultLabel} ${formattedAmount}`;
  }
}
