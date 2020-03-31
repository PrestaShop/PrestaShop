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
import {EventEmitter} from '@components/event-emitter';
import OrderViewEventMap from '@pages/order/view/order-view-event-map';
import OrderPrices from '@pages/order/view/order-prices';

const $ = window.$;

export default class OrderProductEdit {
  constructor(orderDetailId) {
    this.router = new Router();
    this.orderDetailId = orderDetailId;
    this.productRow = $(`#orderProduct_${this.orderDetailId}`);
    this.product = {};
    this.currencyPrecision = $(OrderViewPageMap.productsTable).data('currencyPrecision');
    this.priceTaxCalculator = new OrderPrices();
    this.productEditSaveBtn = $(OrderViewPageMap.productEditSaveBtn);
    this.quantityInput = $(OrderViewPageMap.productEditQuantityInput);
  }

  setupListener() {
    this.quantityInput.on('change keyup', (event) => {
      const quantity = Number(event.target.value);
      const available = parseInt($(event.currentTarget).data('availableQuantity'), 10) - (this.quantity - this.quantityInput.data('previousQuantity'));
      const availableOutOfStock = this.availableText.data('availableOutOfStock');
      this.availableText.text(available);
      this.availableText.toggleClass('text-danger font-weight-bold', available < 0);
      this.updateTotal();
      const disableEditActionBtn = quantity <= 0 || (available <= 0 && !availableOutOfStock) ? true : false;
      this.productEditSaveBtn.prop('disabled', disableEditActionBtn);
    });
    this.productEditInvoiceSelect.on('change', () => {
      this.productEditSaveBtn.prop('disabled', false);
    });
    this.priceTaxIncludedInput.on('change keyup', (event) => {
      this.taxIncluded = parseFloat(event.target.value);
      const taxExcluded = this.priceTaxCalculator.calculateTaxExcluded(
        this.taxIncluded,
        this.taxRate,
        this.currencyPrecision
      );
      this.priceTaxExcludedInput.val(taxExcluded);
      this.updateTotal();
    });
    this.priceTaxExcludedInput.on('change keyup', (event) => {
      const taxExcluded = parseFloat(event.target.value);
      this.taxIncluded = this.priceTaxCalculator.calculateTaxIncluded(
        taxExcluded,
        this.taxRate,
        this.currencyPrecision
      );
      this.priceTaxIncludedInput.val(this.taxIncluded);
      this.updateTotal();
    });
    this.productEditSaveBtn.on('click', (event) => {
      const $btn = $(event.currentTarget);
      const confirmed = window.confirm($btn.data('updateMessage'));
      if (!confirmed) {
        return;
      }
      $btn.prop('disabled', true);
      this.editProduct(
        $(event.currentTarget).data('orderId'),
        this.orderDetailId
      );
    });
    this.productEditCancelBtn.on('click', () => {
      EventEmitter.emit(OrderViewEventMap.productEditionCanceled, {orderDetailId: this.orderDetailId});
    });
  }

  updateTotal() {
    const updatedTotal = this.priceTaxCalculator.calculateTotalPrice(this.quantity, this.taxIncluded, this.currencyPrecision);
    this.priceTotalText.html(updatedTotal);
    this.productEditSaveBtn.prop('disabled', updatedTotal === this.initialTotal);
  }

  displayProduct(product) {
    this.productRowEdit = $(OrderViewPageMap.productEditRowTemplate).clone(true);
    this.productRowEdit.attr('id', `editOrderProduct_${this.orderDetailId}`);

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
      window.ps_round(product.price_tax_excl, this.currencyPrecision)
    );
    this.priceTaxIncludedInput.val(
      window.ps_round(product.price_tax_incl, this.currencyPrecision)
    );
    this.quantityInput.val(product.quantity).data('availableQuantity', product.availableQuantity).data('previousQuantity', product.quantity);
    this.availableText.data('availableOutOfStock', product.availableOutOfStock);

    // set this product's orderInvoiceId as selected
    if (product.orderInvoiceId) {
      this.productEditInvoiceSelect.val(product.orderInvoiceId);
    }


    // Init editor data
    this.taxRate = product.tax_rate;
    this.initialTotal = this.priceTaxCalculator.calculateTotalPrice(
      product.quantity,
      product.price_tax_incl,
      this.currencyPrecision
    );
    this.quantity = product.quantity;
    this.taxIncluded = product.price_tax_incl;

    // Copy product content in cells
    this.productEditImage.html(this.productRow.find(OrderViewPageMap.productEditImage).html());
    this.productEditName.html(this.productRow.find(OrderViewPageMap.productEditName).html());
    this.locationText.html(product.location);
    this.availableText.html(product.availableQuantity);
    this.priceTotalText.html(this.initialTotal);

    this.productRow.addClass('d-none').after(this.productRowEdit.removeClass('d-none'));

    this.setupListener();
  }

  editProduct(orderId, orderDetailId) {
    const params = {
      price_tax_incl: this.priceTaxIncludedInput.val(),
      price_tax_excl: this.priceTaxExcludedInput.val(),
      quantity: this.quantityInput.val(),
      invoice: this.productEditInvoiceSelect.val(),
    };

    $.ajax({
      url: this.router.generate('admin_orders_update_product', {orderId, orderDetailId}),
      method: 'POST',
      data: params,
    }).then((response) => {
      EventEmitter.emit(OrderViewEventMap.productUpdated, {
        orderId,
        orderDetailId,
        newRow: response
      });
    }, (response) => {
      if (response.responseJSON && response.responseJSON.message) {
        $.growl.error({message: response.responseJSON.message});
      }
    });
  }
}
