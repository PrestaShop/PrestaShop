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

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import {EventEmitter} from '@components/event-emitter';
import OrderViewEventMap from '@pages/order/view/order-view-event-map';
import OrderPrices from '@pages/order/view/order-prices';
import ConfirmModal from '@components/modal';
import OrderPricesRefresher from '@pages/order/view/order-prices-refresher';

export interface DisplayedProduct {
  /* eslint-disable camelcase */
  price_tax_excl: number;
  price_tax_incl: number;
  tax_rate: number;
  /* eslint-enable camelcase */
  quantity: number;
  location: string;
  availableQuantity: number;
  availableOutOfStock: string;
  orderInvoiceId: string;
  isOrderTaxIncluded: number;
}

const {$} = window;

export default class OrderProductEdit {
  router: Router;

  orderDetailId: number;

  productRow: JQuery;

  product: Record<string, any>;

  currencyPrecision: number;

  priceTaxCalculator: OrderPrices;

  productEditSaveBtn: JQuery;

  quantityInput: JQuery;

  orderPricesRefresher: OrderPricesRefresher;

  availableText: null | JQuery;

  productEditInvoiceSelect: JQuery | null;

  priceTaxIncludedInput: JQuery | null;

  taxExcluded: number | null;

  taxIncluded: number | null;

  taxRate: number | null;

  priceTaxExcludedInput: JQuery | null;

  productEditCancelBtn: JQuery | null;

  quantity: number | null;

  priceTotalText: JQuery | null;

  initialTotal: number | null;

  productRowEdit: JQuery | null;

  productEditImage: JQuery | null;

  productEditName: JQuery | null;

  locationText: JQuery | null;

  isOrderTaxIncluded: number | null;

  constructor(orderDetailId: number) {
    this.router = new Router();
    this.orderDetailId = orderDetailId;
    this.productRow = $(`#orderProduct_${this.orderDetailId}`);
    this.product = {};
    this.currencyPrecision = $(OrderViewPageMap.productsTable).data('currencyPrecision');
    this.priceTaxCalculator = new OrderPrices();
    this.productEditSaveBtn = $(OrderViewPageMap.productEditSaveBtn);
    this.quantityInput = $(OrderViewPageMap.productEditQuantityInput);
    this.orderPricesRefresher = new OrderPricesRefresher();
    this.availableText = null;
    this.isOrderTaxIncluded = null;
    this.productEditInvoiceSelect = null;
    this.priceTaxIncludedInput = null;
    this.taxExcluded = null;
    this.taxIncluded = null;
    this.taxRate = null;
    this.priceTaxExcludedInput = null;
    this.productEditCancelBtn = null;
    this.quantity = null;
    this.priceTotalText = null;
    this.initialTotal = null;
    this.productRowEdit = null;
    this.productEditImage = null;
    this.productEditName = null;
    this.locationText = null;
  }

  setupListener(): void {
    this.quantityInput.on('change keyup', (event: JQueryEventObject) => {
      const qtyInput = <HTMLInputElement>event.target;
      const newQuantity = Number(qtyInput.value);
      const availableQuantity = parseInt($(event.currentTarget).data('availableQuantity'), 10);
      const previousQuantity = parseInt(this.quantityInput.data('previousQuantity'), 10);
      const remainingAvailable = availableQuantity - (newQuantity - previousQuantity);
      const availableOutOfStock = this.availableText?.data('availableOutOfStock');
      this.quantity = newQuantity;
      if (this.availableText) {
        this.availableText.text(remainingAvailable);
        this.availableText.toggleClass('text-danger font-weight-bold', remainingAvailable < 0);
      }
      this.updateTotal();
      const disableEditActionBtn = newQuantity <= 0 || (remainingAvailable < 0 && !availableOutOfStock);
      this.productEditSaveBtn.prop('disabled', disableEditActionBtn);
    });

    if (this.productEditInvoiceSelect) {
      this.productEditInvoiceSelect.on('change', () => {
        this.productEditSaveBtn.prop('disabled', false);
      });
    }

    if (this.priceTaxIncludedInput) {
      this.priceTaxIncludedInput.on('change keyup', (event) => {
        const input = <HTMLInputElement>event.target;
        this.taxIncluded = parseFloat(input.value);
        this.taxExcluded = this.priceTaxCalculator.calculateTaxExcluded(
          this.taxIncluded,
          <number> this.taxRate,
          this.currencyPrecision,
        );
        if (this.priceTaxExcludedInput) {
          this.priceTaxExcludedInput.val(this.taxExcluded);
        }
        this.updateTotal();
      });
    }

    if (this.priceTaxExcludedInput) {
      this.priceTaxExcludedInput.on('change keyup', (event) => {
        const input = <HTMLInputElement>event.target;
        this.taxExcluded = parseFloat(input.value);
        this.taxIncluded = this.priceTaxCalculator.calculateTaxIncluded(
          this.taxExcluded,
          <number> this.taxRate,
          this.currencyPrecision,
        );
        if (this.priceTaxIncludedInput) {
          this.priceTaxIncludedInput.val(this.taxIncluded);
        }
        this.updateTotal();
      });
    }

    this.productEditSaveBtn.on('click', (event: JQueryEventObject) => {
      const $btn = $(event.currentTarget);
      const confirmed = window.confirm($btn.data('updateMessage'));

      if (!confirmed) {
        return;
      }

      $btn.prop('disabled', true);
      this.handleEditProductWithConfirmationModal(event);
    });

    if (this.productEditCancelBtn) {
      this.productEditCancelBtn.on('click', () => {
        EventEmitter.emit(OrderViewEventMap.productEditionCanceled, {
          orderDetailId: this.orderDetailId,
        });
      });
    }
  }

  updateTotal(): void {
    const updatedTotal = this.priceTaxCalculator.calculateTotalPrice(
      <number> this.quantity,
      this.isOrderTaxIncluded ? <number> this.taxIncluded : <number> this.taxExcluded,
      this.currencyPrecision,
    );

    if (this.priceTotalText) {
      this.priceTotalText.html(<string><unknown>updatedTotal);
    }

    this.productEditSaveBtn.prop('disabled', updatedTotal === this.initialTotal);
  }

  displayProduct(product: DisplayedProduct): void {
    this.productRowEdit = $(OrderViewPageMap.productEditRowTemplate).clone(true);
    this.productRowEdit.attr('id', `editOrderProduct_${this.orderDetailId}`);
    this.productRowEdit.find('*[id]').each(function removeAllIds() {
      $(this).removeAttr('id');
    });

    // Find controls
    this.productEditSaveBtn = this.productRowEdit.find(OrderViewPageMap.productEditSaveBtn);
    this.productEditCancelBtn = this.productRowEdit.find(OrderViewPageMap.productEditCancelBtn);
    this.productEditInvoiceSelect = this.productRowEdit.find(OrderViewPageMap.productEditInvoiceSelect);
    this.productEditImage = this.productRowEdit.find(OrderViewPageMap.productEditImage);
    this.productEditName = this.productRowEdit.find(OrderViewPageMap.productEditName);
    this.priceTaxIncludedInput = this.productRowEdit.find(OrderViewPageMap.productEditPriceTaxInclInput);
    this.priceTaxExcludedInput = this.productRowEdit.find(OrderViewPageMap.productEditPriceTaxExclInput);
    this.quantityInput = this.productRowEdit.find(OrderViewPageMap.productEditQuantityInput);
    this.locationText = this.productRowEdit.find(OrderViewPageMap.productEditLocationText);
    this.availableText = this.productRowEdit.find(OrderViewPageMap.productEditAvailableText);
    this.priceTotalText = this.productRowEdit.find(OrderViewPageMap.productEditTotalPriceText);

    // Init input values
    this.priceTaxExcludedInput.val(
      window.ps_round(product.price_tax_excl, this.currencyPrecision),
    );
    this.priceTaxIncludedInput.val(
      window.ps_round(product.price_tax_incl, this.currencyPrecision),
    );
    this.quantityInput.val(product.quantity)
      .data('availableQuantity', product.availableQuantity)
      .data('previousQuantity', product.quantity);
    this.availableText.data('availableOutOfStock', product.availableOutOfStock);

    // set this product's orderInvoiceId as selected
    if (product.orderInvoiceId) {
      this.productEditInvoiceSelect.val(product.orderInvoiceId);
    }

    // Init editor data
    this.taxRate = product.tax_rate;
    this.initialTotal = this.priceTaxCalculator.calculateTotalPrice(
      product.quantity,
      product.isOrderTaxIncluded ? product.price_tax_incl : product.price_tax_excl,
      this.currencyPrecision,
    );
    this.isOrderTaxIncluded = product.isOrderTaxIncluded;
    this.quantity = product.quantity;
    this.taxIncluded = product.price_tax_incl;
    this.taxExcluded = product.price_tax_excl;

    // Copy product content in cells
    this.productEditImage.html(
      this.productRow.find(OrderViewPageMap.productEditImage).html(),
    );
    this.productEditName.html(
      this.productRow.find(OrderViewPageMap.productEditName).html(),
    );
    this.locationText.html(product.location);
    this.availableText.html(<string><unknown>product.availableQuantity);
    this.priceTotalText.html(<string><unknown> this.initialTotal);
    this.productRow.addClass('d-none').after(this.productRowEdit.removeClass('d-none'));

    this.setupListener();
  }

  handleEditProductWithConfirmationModal(event: JQueryEventObject): void {
    const productEditBtn = $(`#orderProduct_${this.orderDetailId} ${OrderViewPageMap.productEditButtons}`);
    const productId = productEditBtn.data('product-id');
    const combinationId = productEditBtn.data('combination-id');
    const orderInvoiceId = productEditBtn.data('order-invoice-id');
    let productPriceMatch;

    if (this.priceTaxIncludedInput) {
      productPriceMatch = this.orderPricesRefresher.checkOtherProductPricesMatch(
        <number> this.priceTaxIncludedInput.val(),
        productId,
        combinationId,
        orderInvoiceId,
        <number> this.orderDetailId,
      );
    }

    if (productPriceMatch === null) {
      this.editProduct($(event.currentTarget).data('orderId'), this.orderDetailId);

      return;
    }

    const dataSelector = productPriceMatch === 'product' ? this.priceTaxExcludedInput : this.productEditInvoiceSelect;

    if (dataSelector) {
      const modalEditPrice = new ConfirmModal(
        {
          id: 'modal-confirm-new-price',
          confirmTitle: dataSelector.data('modal-edit-price-title'),
          confirmMessage: dataSelector.data('modal-edit-price-body'),
          confirmButtonLabel: dataSelector.data('modal-edit-price-apply'),
          closeButtonLabel: dataSelector.data('modal-edit-price-cancel'),
        },
        () => {
          this.editProduct($(event.currentTarget).data('orderId'), this.orderDetailId);
        },
      );

      modalEditPrice.show();
    }
  }

  editProduct(orderId: number, orderDetailId: number): void {
    const params = {
      price_tax_incl: this.priceTaxIncludedInput?.val(),
      price_tax_excl: this.priceTaxExcludedInput?.val(),
      quantity: this.quantityInput.val(),
      invoice: this.productEditInvoiceSelect?.val(),
    };

    $.ajax({
      url: this.router.generate('admin_orders_update_product', {
        orderId,
        orderDetailId,
      }),
      method: 'POST',
      data: params,
    }).then(
      () => {
        EventEmitter.emit(OrderViewEventMap.productUpdated, {
          orderId,
          orderDetailId,
        });
      },
      (response) => {
        if (response.responseJSON && response.responseJSON.message) {
          $.growl.error({message: response.responseJSON.message});
        }
      },
    );
  }
}
