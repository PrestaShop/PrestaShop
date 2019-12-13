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

import Router from '../../../components/router';
import OrderViewPageMap from '../OrderViewPageMap';
import {EventEmitter} from '../../../components/event-emitter';
import OrderViewEventMap from './order-view-event-map';
import OrderPrices from "./order-prices";

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
      if (this.available === null) {
        return;
      }
      const quantity = parseInt(event.target.value ? event.target.value : 0, 10);
      const available = this.available - quantity;
      this.availableText.text(available);
      this.availableText.toggleClass('text-danger font-weight-bold', available < 0);
      if (available < 0) {
        this.productAddActionBtn.attr('disabled', 'disabled');
      } else {
        this.productAddActionBtn.removeAttr('disabled');
      }

      const priceTaxCalculator = new OrderPrices();
      const taxIncluded = parseFloat(this.priceTaxIncludedInput.val());
      this.totalPriceText.html(
        priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded)
      );
    });
    this.productIdInput.on('change', () => {
      this.productAddActionBtn.removeAttr('disabled');
    });
    this.priceTaxIncludedInput.on('change keyup', (event) => {
      const priceTaxCalculator = new OrderPrices();
      const taxIncluded = parseFloat(event.target.value);
      const taxExcluded = priceTaxCalculator.calculateTaxExcluded(
        taxIncluded,
        this.taxRateInput.val(),
      );
      const quantity = parseInt(this.quantityInput.val(), 10);
      this.priceTaxExcludedInput.val(taxExcluded);
      this.totalPriceText.html(
        priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded)
      );
    });
    this.priceTaxExcludedInput.on('change keyup', (event) => {
      const priceTaxCalculator = new OrderPrices();
      const taxExcluded = parseFloat(event.target.value);
      const taxIncluded = priceTaxCalculator.calculateTaxIncluded(taxExcluded, this.taxRateInput.val());
      const quantity = parseInt(this.quantityInput.val(), 10);
      this.priceTaxIncludedInput.val(taxIncluded);
      this.totalPriceText.html(
        priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded)
      );
    });
    this.productAddActionBtn.on('click', event => this.addProduct($(event.currentTarget).data('orderId')));
  }

  setProduct(product) {
    this.productIdInput.val(product.productId).trigger('change');
    this.priceTaxExcludedInput.val(product.priceTaxExcl);
    this.priceTaxIncludedInput.val(product.priceTaxIncl);
    this.taxRateInput.val(product.taxRate);
    this.locationText.html(product.location);
    this.available = product.stock;
    this.quantityInput.val(1);
    this.quantityInput.trigger('change');
    this.setCombinations(product.combinations);
  }

  setCombinations(combinations) {
    this.combinationsSelect.empty();
    Object.entries(combinations).forEach((val) => {
      this.combinationsSelect.append(`<option value="${val[1].attributeCombinationId}" data-price-tax-excluded="${val[1].priceTaxExcluded}" data-price-tax-included="${val[1].priceTaxIncluded}" data-stock="${val[1].stock}">${val[1].attribute}</option>`);
    });
    this.combinationsBlock.toggleClass('d-none', Object.keys(combinations).length === 0);
  }

  addProduct(orderId) {
    this.productAddActionBtn.attr('disabled', 'disabled');

    const params = {
      product_id: this.productIdInput.val(),
      combination_id: $(':selected', this.combinationsSelect).val(),
      price_tax_incl: this.priceTaxIncludedInput.val(),
      price_tax_excl: this.priceTaxExcludedInput.val(),
      quantity: this.quantityInput.val(),
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
