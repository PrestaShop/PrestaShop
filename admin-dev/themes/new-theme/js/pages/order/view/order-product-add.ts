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

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import {EventEmitter} from '@components/event-emitter';
import OrderViewEventMap from '@pages/order/view/order-view-event-map';
import OrderPrices from '@pages/order/view/order-prices';
import OrderProductRenderer from '@pages/order/view/order-product-renderer';
import ConfirmModal from '@components/modal';
import OrderPricesRefresher from '@pages/order/view/order-prices-refresher';

const {$} = window;

export default class OrderProductAdd {
  router: Router;

  productAddActionBtn: JQuery;

  productIdInput: JQuery;

  combinationsBlock: JQuery;

  combinationsSelect: JQuery;

  priceTaxIncludedInput: JQuery;

  priceTaxExcludedInput: JQuery;

  taxRateInput: JQuery;

  quantityInput: JQuery;

  availableText: JQuery;

  locationText: JQuery;

  totalPriceText: JQuery;

  invoiceSelect: JQuery;

  freeShippingSelect: JQuery;

  productAddMenuBtn: JQuery;

  available: number | null;

  product: Record<string, any>;

  currencyPrecision: number;

  priceTaxCalculator: OrderPrices;

  orderProductRenderer: OrderProductRenderer;

  orderPricesRefresher: OrderPricesRefresher;

  isOrderTaxIncluded: boolean;

  taxExcluded: number | null;

  taxIncluded: number | null;

  constructor() {
    this.router = new Router();
    this.productAddActionBtn = $(OrderViewPageMap.productAddActionBtn);
    this.productIdInput = $(OrderViewPageMap.productAddIdInput);
    this.combinationsBlock = $(OrderViewPageMap.productAddCombinationsBlock);
    this.combinationsSelect = $(OrderViewPageMap.productAddCombinationsSelect);
    this.priceTaxIncludedInput = $(
      OrderViewPageMap.productAddPriceTaxInclInput,
    );
    this.priceTaxExcludedInput = $(
      OrderViewPageMap.productAddPriceTaxExclInput,
    );
    this.taxRateInput = $(OrderViewPageMap.productAddTaxRateInput);
    this.quantityInput = $(OrderViewPageMap.productAddQuantityInput);
    this.availableText = $(OrderViewPageMap.productAddAvailableText);
    this.locationText = $(OrderViewPageMap.productAddLocationText);
    this.totalPriceText = $(OrderViewPageMap.productAddTotalPriceText);
    this.invoiceSelect = $(OrderViewPageMap.productAddInvoiceSelect);
    this.freeShippingSelect = $(OrderViewPageMap.productAddFreeShippingSelect);
    this.productAddMenuBtn = $(OrderViewPageMap.productAddBtn);
    this.available = null;
    this.setupListener();
    this.product = {};
    this.currencyPrecision = $(OrderViewPageMap.productsTable).data(
      'currencyPrecision',
    );
    this.priceTaxCalculator = new OrderPrices();
    this.orderProductRenderer = new OrderProductRenderer();
    this.orderPricesRefresher = new OrderPricesRefresher();
    this.isOrderTaxIncluded = $(OrderViewPageMap.productAddRow).data('isOrderTaxIncluded');
    this.taxExcluded = null;
    this.taxIncluded = null;
  }

  setupListener(): void {
    this.combinationsSelect.on('change', (event) => {
      const taxExcluded = window.ps_round(
        $(event.currentTarget)
          .find(':selected')
          .data('priceTaxExcluded'),
        this.currencyPrecision,
      );
      this.priceTaxExcludedInput.val(taxExcluded);
      this.taxExcluded = parseFloat(taxExcluded);

      const taxIncluded = window.ps_round(
        $(event.currentTarget)
          .find(':selected')
          .data('priceTaxIncluded'),
        this.currencyPrecision,
      );
      this.priceTaxIncludedInput.val(taxIncluded);
      this.taxIncluded = parseFloat(taxIncluded);

      this.locationText.html(
        $(event.currentTarget)
          .find(':selected')
          .data('location'),
      );

      this.available = $(event.currentTarget)
        .find(':selected')
        .data('stock');

      this.quantityInput.trigger('change');
      this.orderProductRenderer.toggleColumn(
        OrderViewPageMap.productsCellLocation,
      );
    });

    this.quantityInput.on('change keyup', (event: JQueryEventObject) => {
      if (this.available !== null) {
        const input = <HTMLInputElement>event.target;
        const newQuantity = Number(input.value);
        const remainingAvailable = this.available - newQuantity;
        const availableOutOfStock = this.availableText.data(
          'availableOutOfStock',
        );
        this.availableText.text(remainingAvailable);
        this.availableText.toggleClass(
          'text-danger font-weight-bold',
          remainingAvailable < 0,
        );
        const disableAddActionBtn = newQuantity <= 0 || (remainingAvailable < 0 && !availableOutOfStock);
        this.productAddActionBtn.prop('disabled', disableAddActionBtn);
        this.invoiceSelect.prop(
          'disabled',
          !availableOutOfStock && remainingAvailable < 0,
        );

        this.taxIncluded = parseFloat(
          <string> this.priceTaxIncludedInput.val(),
        );
        this.totalPriceText.html(
          <string>(
            (<unknown>(
              this.priceTaxCalculator.calculateTotalPrice(
                newQuantity,
                this.isOrderTaxIncluded ? <number> this.taxIncluded : <number> this.taxExcluded,
                this.currencyPrecision,
              )
            ))
          ),
        );
      }
    });

    this.productIdInput.on('change', () => {
      this.productAddActionBtn.removeAttr('disabled');
      this.invoiceSelect.removeAttr('disabled');
    });

    this.priceTaxIncludedInput.on('change keyup', (event) => {
      const input = <HTMLInputElement>event.target;
      this.taxIncluded = parseFloat(input.value);
      this.taxExcluded = this.priceTaxCalculator.calculateTaxExcluded(
        this.taxIncluded,
        <number> this.taxRateInput.val(),
        this.currencyPrecision,
      );
      const quantity = parseInt(<string> this.quantityInput.val(), 10);

      this.priceTaxExcludedInput.val(this.taxExcluded);
      this.totalPriceText.html(
        <string>(
          (<unknown>(
            this.priceTaxCalculator.calculateTotalPrice(
              quantity,
              this.isOrderTaxIncluded ? this.taxIncluded : this.taxExcluded,
              this.currencyPrecision,
            )
          ))
        ),
      );
    });

    this.priceTaxExcludedInput.on('change keyup', (event) => {
      const input = <HTMLInputElement>event.target;
      this.taxExcluded = parseFloat(input.value);
      this.taxIncluded = this.priceTaxCalculator.calculateTaxIncluded(
        this.taxExcluded,
        <number> this.taxRateInput.val(),
        this.currencyPrecision,
      );
      const quantity = parseInt(<string> this.quantityInput.val(), 10);

      this.priceTaxIncludedInput.val(this.taxIncluded);
      this.totalPriceText.html(
        <string>(
          (<unknown>(
            this.priceTaxCalculator.calculateTotalPrice(
              quantity,
              this.isOrderTaxIncluded ? this.taxIncluded : this.taxExcluded,
              this.currencyPrecision,
            )
          ))
        ),
      );
    });

    this.productAddActionBtn.on('click', (event: JQueryEventObject) => this.confirmNewInvoice(event),
    );
    this.invoiceSelect.on('change', () => this.orderProductRenderer.toggleProductAddNewInvoiceInfo(),
    );
  }

  setProduct(product: Record<string, any> | undefined): void {
    if (product) {
      this.productIdInput.val(product.productId).trigger('change');
      const taxExcluded = window.ps_round(product.priceTaxExcl, this.currencyPrecision);
      this.priceTaxExcludedInput.val(taxExcluded);
      this.taxExcluded = parseFloat(taxExcluded);

      const taxIncluded = window.ps_round(product.priceTaxIncl, this.currencyPrecision);
      this.priceTaxIncludedInput.val(taxIncluded);
      this.taxIncluded = parseFloat(taxIncluded);

      this.taxRateInput.val(product.taxRate);
      this.locationText.html(product.location);
      this.available = product.stock;
      this.availableText.data(
        'availableOutOfStock',
        product.availableOutOfStock,
      );
      this.quantityInput.val(1);
      this.quantityInput.trigger('change');
      this.setCombinations(product.combinations);
      this.orderProductRenderer.toggleColumn(
        OrderViewPageMap.productsCellLocation,
      );
    }
  }

  setCombinations(combinations: Record<string, any>): void {
    this.combinationsSelect.empty();

    Object.values(combinations).forEach((val) => {
      this.combinationsSelect.append(
        /* eslint-disable-next-line max-len */
        `<option value="${val.attributeCombinationId}" data-price-tax-excluded="${val.priceTaxExcluded}" data-price-tax-included="${val.priceTaxIncluded}" data-stock="${val.stock}" data-location="${val.location}">${val.attribute}</option>`,
      );
    });

    this.combinationsBlock.toggleClass(
      'd-none',
      Object.keys(combinations).length === 0,
    );

    if (Object.keys(combinations).length > 0) {
      this.combinationsSelect.trigger('change');
    }
  }

  addProduct(orderId: number): void {
    this.productAddActionBtn.prop('disabled', true);
    this.invoiceSelect.prop('disabled', true);
    this.combinationsSelect.prop('disabled', true);

    const params = {
      product_id: this.productIdInput.val(),
      combination_id: $(':selected', this.combinationsSelect).val(),
      price_tax_incl: this.priceTaxIncludedInput.val(),
      price_tax_excl: this.priceTaxExcludedInput.val(),
      quantity: this.quantityInput.val(),
      invoice_id: this.invoiceSelect.val(),
      free_shipping: this.freeShippingSelect.prop('checked'),
    };

    $.ajax({
      url: this.router.generate('admin_orders_add_product', {orderId}),
      method: 'POST',
      data: params,
    }).then(
      (response) => {
        EventEmitter.emit(OrderViewEventMap.productAddedToOrder, {
          orderId,
          orderProductId: params.product_id,
          newRow: response,
        });
      },
      (response) => {
        this.productAddActionBtn.prop('disabled', false);
        this.invoiceSelect.prop('disabled', false);
        this.combinationsSelect.prop('disabled', false);

        if (response.responseJSON && response.responseJSON.message) {
          $.growl.error({message: response.responseJSON.message});
        }
      },
    );
  }

  confirmNewInvoice(event: JQueryEventObject): void {
    const invoiceId = parseInt(<string> this.invoiceSelect.val(), 10);
    const orderId = $(event.currentTarget).data('orderId');

    // Explicit 0 value is used when we the user selected New Invoice
    if (invoiceId === 0) {
      const modal = new ConfirmModal(
        {
          id: 'modal-confirm-new-invoice',
          confirmTitle: this.invoiceSelect.data('modal-title'),
          confirmMessage: this.invoiceSelect.data('modal-body'),
          confirmButtonLabel: this.invoiceSelect.data('modal-apply'),
          closeButtonLabel: this.invoiceSelect.data('modal-cancel'),
        },
        () => {
          this.confirmNewPrice(orderId, invoiceId);
        },
      );
      modal.show();
    } else {
      // Last case is Nan, the selector is not even present, we simply add product and let the BO handle it
      this.addProduct(orderId);
    }
  }

  confirmNewPrice(orderId: number, invoiceId: number): void {
    const combinationValue = $(':selected', this.combinationsSelect).val();
    const combinationId = typeof combinationValue === 'undefined' ? 0 : combinationValue;
    const productPriceMatch = this.orderPricesRefresher.checkOtherProductPricesMatch(
      <number> this.priceTaxIncludedInput.val(),
      <number> this.productIdInput.val(),
      <number>combinationId,
      invoiceId,
    );

    if (productPriceMatch === 'invoice') {
      const modalEditPrice = new ConfirmModal(
        {
          id: 'modal-confirm-new-price',
          confirmTitle: this.invoiceSelect.data('modal-edit-price-title'),
          confirmMessage: this.invoiceSelect.data('modal-edit-price-body'),
          confirmButtonLabel: this.invoiceSelect.data('modal-edit-price-apply'),
          closeButtonLabel: this.invoiceSelect.data('modal-edit-price-cancel'),
        },
        () => {
          this.addProduct(orderId);
        },
      );
      modalEditPrice.show();
    } else {
      this.addProduct(orderId);
    }
  }
}
