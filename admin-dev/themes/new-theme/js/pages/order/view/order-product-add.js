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

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import {EventEmitter} from '@components/event-emitter';
import OrderViewEventMap from '@pages/order/view/order-view-event-map';
import OrderPrices from '@pages/order/view/order-prices';

const $ = window.$;

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
    this.available = null;
    this.setupListener();
    this.product = {};
  }

  setupListener() {
    this.combinationsSelect.on('change', (event) => {
      this.priceTaxExcludedInput.val($(event.currentTarget).find(':selected').data('priceTaxExcluded'));
      this.priceTaxIncludedInput.val($(event.currentTarget).find(':selected').data('priceTaxIncluded'));
      this.available = $(event.currentTarget).find(':selected').data('stock');
      this.quantityInput.trigger('change');
    });
    this.quantityInput.on('change keyup', (event) => {
      if (this.available !== null) {
        const quantity = parseInt(event.target.value ? event.target.value : 0, 10);
        const available = this.available - quantity;
        const availableOutOfStock = this.availableText.data('availableOutOfStock');
        this.availableText.text(available);
        this.availableText.toggleClass('text-danger font-weight-bold', available < 0);
        this.productAddActionBtn.prop('disabled', !availableOutOfStock && available < 0);
        this.invoiceSelect.prop('disabled', !availableOutOfStock && available < 0);

        const currencyPrecision = $(OrderViewPageMap.productsTable).data('currencyPrecision');
        const priceTaxCalculator = new OrderPrices();
        const taxIncluded = parseFloat(this.priceTaxIncludedInput.val());
        this.totalPriceText.html(
          priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded, currencyPrecision)
        );
      }
    });
    this.productIdInput.on('change', () => {
      this.productAddActionBtn.removeAttr('disabled');
      this.invoiceSelect.removeAttr('disabled');
    });
    this.priceTaxIncludedInput.on('change keyup', (event) => {
      const currencyPrecision = $(OrderViewPageMap.productsTable).data('currencyPrecision');
      const priceTaxCalculator = new OrderPrices();
      const taxIncluded = parseFloat(event.target.value);
      const taxExcluded = priceTaxCalculator.calculateTaxExcluded(
        taxIncluded,
        this.taxRateInput.val(),
        currencyPrecision
      );
      const quantity = parseInt(this.quantityInput.val(), 10);
      this.priceTaxExcludedInput.val(taxExcluded);
      this.totalPriceText.html(
        priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded, currencyPrecision)
      );
    });
    this.priceTaxExcludedInput.on('change keyup', (event) => {
      const currencyPrecision = $(OrderViewPageMap.productsTable).data('currencyPrecision');
      const priceTaxCalculator = new OrderPrices();
      const taxExcluded = parseFloat(event.target.value);
      const taxIncluded = priceTaxCalculator.calculateTaxIncluded(
        taxExcluded,
        this.taxRateInput.val(),
        currencyPrecision
      );
      const quantity = parseInt(this.quantityInput.val(), 10);
      this.priceTaxIncludedInput.val(taxIncluded);
      this.totalPriceText.html(
        priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded, currencyPrecision)
      );
    });
    this.productAddActionBtn.on('click', event => this.addProduct($(event.currentTarget).data('orderId')));
  }

  setProduct(product) {
    const currencyPrecision = $(OrderViewPageMap.productsTable).data('currencyPrecision');
    this.productIdInput.val(product.productId).trigger('change');
    this.priceTaxExcludedInput.val(window.ps_round(product.priceTaxExcl, currencyPrecision));
    this.priceTaxIncludedInput.val(window.ps_round(product.priceTaxIncl, currencyPrecision));
    this.taxRateInput.val(product.taxRate);
    this.locationText.html(product.location);
    this.available = product.stock;
    this.availableText.data('availableOutOfStock', product.availableOutOfStock);
    this.quantityInput.val(1);
    this.quantityInput.trigger('change');
    this.setCombinations(product.combinations);
  }

  setCombinations(combinations) {
    this.combinationsSelect.empty();
    Object.values(combinations).forEach((val) => {
      this.combinationsSelect.append(`<option value="${val.attributeCombinationId}" data-price-tax-excluded="${val.priceTaxExcluded}" data-price-tax-included="${val.priceTaxIncluded}" data-stock="${val.stock}">${val.attribute}</option>`);
    });
    this.combinationsBlock.toggleClass('d-none', Object.keys(combinations).length === 0);
  }

  addProduct(orderId) {
    this.productAddActionBtn.prop('disabled', true);
    this.invoiceSelect.prop('disabled', true);
    this.combinationsBlock.toggleClass('d-none', true);

    const params = {
      product_id: this.productIdInput.val(),
      combination_id: $(':selected', this.combinationsSelect).val(),
      price_tax_incl: this.priceTaxIncludedInput.val(),
      price_tax_excl: this.priceTaxExcludedInput.val(),
      quantity: this.quantityInput.val(),
      invoice_id: this.invoiceSelect.val(),
    };
    $.ajax({
      url: this.router.generate('admin_orders_add_product', {orderId}),
      method: 'POST',
      data: params,
    }).then((response) => {
      EventEmitter.emit(OrderViewEventMap.productAddedToOrder, {
        orderId,
        orderProductId: params.product_id,
        newRow: response,
      });
    }, (response) => {
      if (response.message) {
        $.growl.error({message: response.message});
      }
    });
  }
}
