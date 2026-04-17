/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import {NumberFormatter} from '@app/cldr';

const {$} = window;

/**
 * manages all product cancel actions, that includes all refund operations
 */
export default class OrderProductCancel {
  router: Router;

  cancelProductForm: JQuery;

  orderId: string;

  orderDelivered: boolean;

  isTaxIncluded: boolean;

  discountsAmount: number;

  currencyFormatter: NumberFormatter;

  useAmountInputs: boolean;

  constructor() {
    this.router = new Router();
    this.cancelProductForm = $(OrderViewPageMap.cancelProduct.form);
    this.orderId = this.cancelProductForm.data('orderId');
    this.orderDelivered = parseInt(this.cancelProductForm.data('isDelivered'), 10) === 1;
    this.isTaxIncluded = parseInt(this.cancelProductForm.data('isTaxIncluded'), 10) === 1;
    this.discountsAmount = parseFloat(this.cancelProductForm.data('discountsAmount'));
    this.currencyFormatter = NumberFormatter.build(
      this.cancelProductForm.data('priceSpecification'),
    );
    this.useAmountInputs = true;
    this.listenForInputs();
  }

  showPartialRefund(): void {
    // Always start by hiding elements then show the others, since some elements are common
    this.hideCancelElements();
    $(OrderViewPageMap.cancelProduct.toggle.partialRefund).show();
    this.useAmountInputs = true;
    this.initForm(
      $(OrderViewPageMap.cancelProduct.buttons.save).data('partialRefundLabel'),
      this.router.generate('admin_orders_partial_refund', {
        orderId: this.orderId,
      }),
      'partial-refund',
    );
  }

  showStandardRefund(): void {
    // Always start by hiding elements then show the others, since some elements are common
    this.hideCancelElements();
    $(OrderViewPageMap.cancelProduct.toggle.standardRefund).show();
    this.useAmountInputs = false;
    this.initForm(
      $(OrderViewPageMap.cancelProduct.buttons.save).data('standardRefundLabel'),
      this.router.generate('admin_orders_standard_refund', {
        orderId: this.orderId,
      }),
      'standard-refund',
    );
  }

  showReturnProduct(): void {
    // Always start by hiding elements then show the others, since some elements are common
    this.hideCancelElements();
    $(OrderViewPageMap.cancelProduct.toggle.returnProduct).show();
    this.useAmountInputs = false;
    this.initForm(
      $(OrderViewPageMap.cancelProduct.buttons.save).data('returnProductLabel'),
      this.router.generate('admin_orders_return_product', {
        orderId: this.orderId,
      }),
      'return-product',
    );
  }

  hideRefund(): void {
    this.hideCancelElements();
    $(OrderViewPageMap.cancelProduct.table.actions).show();
  }

  hideCancelElements(): void {
    $(OrderViewPageMap.cancelProduct.toggle.standardRefund).hide();
    $(OrderViewPageMap.cancelProduct.toggle.partialRefund).hide();
    $(OrderViewPageMap.cancelProduct.toggle.returnProduct).hide();
    $(OrderViewPageMap.cancelProduct.table.actions).hide();
  }

  initForm(actionName: string, formAction: string, formClass: string): void {
    this.updateVoucherRefund();

    this.cancelProductForm.prop('action', formAction);
    this.cancelProductForm
      .removeClass('standard-refund partial-refund return-product cancel-product')
      .addClass(formClass);
    $(OrderViewPageMap.cancelProduct.buttons.save).html(actionName);
    $(OrderViewPageMap.cancelProduct.table.header).html(actionName);
    $(OrderViewPageMap.cancelProduct.checkboxes.restock).prop('checked', this.orderDelivered);
    $(OrderViewPageMap.cancelProduct.checkboxes.creditSlip).prop('checked', true);
    $(OrderViewPageMap.cancelProduct.checkboxes.voucher).prop('checked', false);
  }

  listenForInputs(): void {
    $(document).on('change', OrderViewPageMap.cancelProduct.inputs.quantity, (event) => {
      const $productQuantityInput = $(event.target);
      const $parentCell = $productQuantityInput.parents(OrderViewPageMap.cancelProduct.table.cell);
      const $productAmount = $parentCell.find(OrderViewPageMap.cancelProduct.inputs.amount);
      const productQuantity = parseInt(<string>$productQuantityInput.val(), 10);

      if (productQuantity <= 0) {
        $productAmount.val(0);
        this.updateVoucherRefund();

        return;
      }
      const priceFieldName = this.isTaxIncluded ? 'productPriceTaxIncl' : 'productPriceTaxExcl';
      const productUnitPrice = parseFloat($productQuantityInput.data(priceFieldName));
      const amountRefundable = parseFloat($productQuantityInput.data('amountRefundable'));
      const guessedAmount = productUnitPrice * productQuantity < amountRefundable
        ? productUnitPrice * productQuantity
        : amountRefundable;
      const amountValue = parseFloat(<string>$productAmount.val());

      if (this.useAmountInputs) {
        this.updateAmountInput($productQuantityInput);
      }

      if ($productAmount.val() === '' || amountValue === 0 || amountValue > guessedAmount) {
        $productAmount.val(guessedAmount);
        this.updateVoucherRefund();
      }
    });

    $(document).on('change', OrderViewPageMap.cancelProduct.inputs.amount, () => {
      this.updateVoucherRefund();
    });

    $(document).on('change', OrderViewPageMap.cancelProduct.inputs.selector, (event) => {
      const $productCheckbox = $(event.target);
      const $parentCell = $productCheckbox.parents(OrderViewPageMap.cancelProduct.table.cell);
      const productQuantityInput = $parentCell.find(OrderViewPageMap.cancelProduct.inputs.quantity);
      const refundableQuantity = parseInt(productQuantityInput.data('quantityRefundable'), 10);
      const productQuantity = parseInt(<string>productQuantityInput.val(), 10);

      if (!$productCheckbox.is(':checked')) {
        productQuantityInput.val(0);
      } else if (Number.isNaN(productQuantity) || productQuantity === 0) {
        productQuantityInput.val(refundableQuantity);
      }
      this.updateVoucherRefund();
    });
  }

  updateAmountInput($productQuantityInput: JQuery): void {
    const $parentCell = $productQuantityInput.parents(OrderViewPageMap.cancelProduct.table.cell);
    const $productAmount = $parentCell.find(OrderViewPageMap.cancelProduct.inputs.amount);
    const productQuantity = parseInt(<string>$productQuantityInput.val(), 10);

    if (productQuantity <= 0) {
      $productAmount.val(0);

      return;
    }

    const priceFieldName = this.isTaxIncluded ? 'productPriceTaxIncl' : 'productPriceTaxExcl';
    const productUnitPrice = parseFloat($productQuantityInput.data(priceFieldName));
    const amountRefundable = parseFloat($productQuantityInput.data('amountRefundable'));
    const guessedAmount = productUnitPrice * productQuantity < amountRefundable
      ? productUnitPrice * productQuantity
      : amountRefundable;
    const amountValue = parseFloat(<string>$productAmount.val());

    if ($productAmount.val() === '' || amountValue === 0 || amountValue > guessedAmount) {
      $productAmount.val(guessedAmount);
    }
  }

  getRefundAmount(): number {
    let totalAmount = 0;

    if (this.useAmountInputs) {
      $(OrderViewPageMap.cancelProduct.inputs.amount).each((index, amount) => {
        const input = <HTMLInputElement>amount;
        const floatValue = parseFloat(input.value);
        totalAmount += !Number.isNaN(floatValue) ? floatValue : 0;
      });
    } else {
      $(OrderViewPageMap.cancelProduct.inputs.quantity).each((index, quantity) => {
        const $quantityInput = $(quantity);
        const priceFieldName = this.isTaxIncluded ? 'productPriceTaxIncl' : 'productPriceTaxExcl';
        const productUnitPrice = parseFloat($quantityInput.data(priceFieldName));
        const productQuantity = parseInt(<string>$quantityInput.val(), 10);
        totalAmount += productQuantity * productUnitPrice;
      });
    }

    return totalAmount;
  }

  updateVoucherRefund(): void {
    const refundAmount = this.getRefundAmount();

    this.updateVoucherRefundTypeLabel(
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.productPrices),
      refundAmount,
    );
    const refundVoucherExcluded = refundAmount - this.discountsAmount;
    this.updateVoucherRefundTypeLabel(
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.productPricesVoucherExcluded),
      refundVoucherExcluded,
    );

    // Disable voucher excluded option when the voucher amount is too high
    if (refundVoucherExcluded < 0) {
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.productPricesVoucherExcluded)
        .prop('checked', false)
        .prop('disabled', true);
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.productPrices).prop(
        'checked',
        true,
      );
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.negativeErrorMessage).show();
    } else {
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.productPricesVoucherExcluded).prop(
        'disabled',
        false,
      );
      $(OrderViewPageMap.cancelProduct.radios.voucherRefundType.negativeErrorMessage).hide();
    }
  }

  updateVoucherRefundTypeLabel($input: JQuery, refundAmount: number): void {
    const defaultLabel = $input.data('defaultLabel');
    const $label = $input.parents('label');
    const formattedAmount = this.currencyFormatter.format(refundAmount);
    const lastChild = $label?.get(0)?.lastChild;

    // Change the ending text part only to avoid removing the input (the EOL is on purpose for better display)
    if (lastChild) {
      lastChild.nodeValue = `
      ${defaultLabel} ${formattedAmount}`;
    }
  }

  showCancelProductForm(): void {
    const cancelProductRoute = this.router.generate('admin_orders_cancellation', {orderId: this.orderId});
    this.initForm(
      $(OrderViewPageMap.cancelProduct.buttons.save).data('cancelLabel'),
      cancelProductRoute,
      'cancel-product',
    );
    this.hideCancelElements();
    $(OrderViewPageMap.cancelProduct.toggle.cancelProducts).show();
  }
}
