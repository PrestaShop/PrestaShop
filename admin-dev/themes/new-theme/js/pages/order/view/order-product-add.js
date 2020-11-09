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
  constructor() {
    this.router = new Router();
    this.productAddActionBtn = $(OrderViewPageMap.productAddActionBtn);
    this.productIdInput = $(OrderViewPageMap.productAddIdInput);
    this.combinationsBlock = $(OrderViewPageMap.productAddCombinationsBlock);
    this.combinationsSelect = $(OrderViewPageMap.productAddCombinationsSelect);
    this.priceTaxIncludedInput = $(OrderViewPageMap.productAddPriceTaxInclInput);
    this.priceTaxExcludedInput = $(OrderViewPageMap.productAddPriceTaxExclInput);
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
    this.currencyPrecision = $(OrderViewPageMap.productsTable).data('currencyPrecision');
    this.priceTaxCalculator = new OrderPrices();
    this.orderProductRenderer = new OrderProductRenderer();
    this.orderPricesRefresher = new OrderPricesRefresher();
  }

  setupListener() {
    this.combinationsSelect.on('change', event => {
      this.priceTaxExcludedInput.val(
        window.ps_round(
          $(event.currentTarget)
            .find(':selected')
            .data('priceTaxExcluded'),
          this.currencyPrecision
        )
      );

      this.priceTaxIncludedInput.val(
        window.ps_round(
          $(event.currentTarget)
            .find(':selected')
            .data('priceTaxIncluded'),
          this.currencyPrecision
        )
      );

      this.locationText.html(
        $(event.currentTarget)
          .find(':selected')
          .data('location')
      );

      this.available = $(event.currentTarget)
        .find(':selected')
        .data('stock');

      this.quantityInput.trigger('change');
      this.orderProductRenderer.toggleColumn(OrderViewPageMap.productsCellLocation);
    });

    this.quantityInput.on('change keyup', event => {
      if (this.available !== null) {
        const newQuantity = Number(event.target.value);
        const remainingAvailable = this.available - newQuantity;
        const availableOutOfStock = this.availableText.data('availableOutOfStock');
        this.availableText.text(remainingAvailable);
        this.availableText.toggleClass('text-danger font-weight-bold', remainingAvailable < 0);
        const disableAddActionBtn = newQuantity <= 0 || (remainingAvailable < 0 && !availableOutOfStock);
        this.productAddActionBtn.prop('disabled', disableAddActionBtn);
        this.invoiceSelect.prop('disabled', !availableOutOfStock && remainingAvailable < 0);

        const taxIncluded = parseFloat(this.priceTaxIncludedInput.val());
        this.totalPriceText.html(
          this.priceTaxCalculator.calculateTotalPrice(newQuantity, taxIncluded, this.currencyPrecision)
        );
      }
    });

    this.productIdInput.on('change', () => {
      this.productAddActionBtn.removeAttr('disabled');
      this.invoiceSelect.removeAttr('disabled');
    });

    this.priceTaxIncludedInput.on('change keyup', event => {
      const taxIncluded = parseFloat(event.target.value);
      const taxExcluded = this.priceTaxCalculator.calculateTaxExcluded(
        taxIncluded,
        this.taxRateInput.val(),
        this.currencyPrecision
      );
      const quantity = parseInt(this.quantityInput.val(), 10);

      this.priceTaxExcludedInput.val(taxExcluded);
      this.totalPriceText.html(
        this.priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded, this.currencyPrecision)
      );
    });

    this.priceTaxExcludedInput.on('change keyup', event => {
      const taxExcluded = parseFloat(event.target.value);
      const taxIncluded = this.priceTaxCalculator.calculateTaxIncluded(
        taxExcluded,
        this.taxRateInput.val(),
        this.currencyPrecision
      );
      const quantity = parseInt(this.quantityInput.val(), 10);

      this.priceTaxIncludedInput.val(taxIncluded);
      this.totalPriceText.html(
        this.priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded, this.currencyPrecision)
      );
    });

    this.productAddActionBtn.on('click', event => this.confirmNewInvoice(event));
    this.invoiceSelect.on('change', () => this.orderProductRenderer.toggleProductAddNewInvoiceInfo());
  }

  setProduct(product) {
    this.productIdInput.val(product.productId).trigger('change');
    this.priceTaxExcludedInput.val(window.ps_round(product.priceTaxExcl, this.currencyPrecision));
    this.priceTaxIncludedInput.val(window.ps_round(product.priceTaxIncl, this.currencyPrecision));
    this.taxRateInput.val(product.taxRate);
    this.locationText.html(product.location);
    this.available = product.stock;
    this.availableText.data('availableOutOfStock', product.availableOutOfStock);
    this.quantityInput.val(1);
    this.quantityInput.trigger('change');
    this.setCombinations(product.combinations);
    this.orderProductRenderer.toggleColumn(OrderViewPageMap.productsCellLocation);
  }

  setCombinations(combinations) {
    this.combinationsSelect.empty();

    Object.values(combinations).forEach(val => {
      this.combinationsSelect.append(
        `<option value="${val.attributeCombinationId}" data-price-tax-excluded="${val.priceTaxExcluded}" data-price-tax-included="${val.priceTaxIncluded}" data-stock="${val.stock}" data-location="${val.location}">${val.attribute}</option>`
      );
    });

    this.combinationsBlock.toggleClass('d-none', Object.keys(combinations).length === 0);

    if (Object.keys(combinations).length > 0) {
      this.combinationsSelect.trigger('change');
    }
  }

  addProduct(orderId) {
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
      free_shipping: this.freeShippingSelect.prop('checked')
    };

    $.ajax({
      url: this.router.generate('admin_orders_add_product', {orderId}),
      method: 'POST',
      data: params
    }).then(
      response => {
        EventEmitter.emit(OrderViewEventMap.productAddedToOrder, {
          orderId,
        });
      },
      response => {
        this.productAddActionBtn.prop('disabled', false);
        this.invoiceSelect.prop('disabled', false);
        this.combinationsSelect.prop('disabled', false);

        if (response.responseJSON && response.responseJSON.message) {
          $.growl.error({message: response.responseJSON.message});
        }
      }
    );
  }

  confirmNewInvoice(event) {
    const invoiceId = parseInt(this.invoiceSelect.val(), 10);
    const orderId = $(event.currentTarget).data('orderId');

    // Explicit 0 value is used when we the user selected New Invoice
    if (invoiceId === 0) {
      const modal = new ConfirmModal(
        {
          id: 'modal-confirm-new-invoice',
          confirmTitle: this.invoiceSelect.data('modal-title'),
          confirmMessage: this.invoiceSelect.data('modal-body'),
          confirmButtonLabel: this.invoiceSelect.data('modal-apply'),
          closeButtonLabel: this.invoiceSelect.data('modal-cancel')
        },
        () => {
          this.confirmNewPrice(orderId, invoiceId);
        }
      );
      modal.show();
    } else if (!isNaN(invoiceId)) {
      // If id is not 0 nor NaN a specific invoice was selected
      this.confirmNewPrice(orderId, invoiceId);
    } else {
      // Last case is Nan, the selector is not even present, we simply add product and let the BO handle it
      this.addProduct(orderId);
    }
  }

  confirmNewPrice(orderId, invoiceId) {
    const combinationId =
      typeof $(':selected', this.combinationsSelect).val() === 'undefined'
        ? 0
        : $(':selected', this.combinationsSelect).val();
    const productPriceMatch = this.orderPricesRefresher.checkOtherProductPricesMatch(
      this.priceTaxIncludedInput.val(),
      this.productIdInput.val(),
      combinationId,
      invoiceId
    );

    if (!productPriceMatch) {
      const modalEditPrice = new ConfirmModal(
        {
          id: 'modal-confirm-new-price',
          confirmTitle: this.invoiceSelect.data('modal-edit-price-title'),
          confirmMessage: this.invoiceSelect.data('modal-edit-price-body'),
          confirmButtonLabel: this.invoiceSelect.data('modal-edit-price-apply'),
          closeButtonLabel: this.invoiceSelect.data('modal-edit-price-cancel')
        },
        () => {
          this.addProduct(orderId);
        }
      );
      modalEditPrice.show();
    } else {
      this.addProduct(orderId);
    }
  }
}
