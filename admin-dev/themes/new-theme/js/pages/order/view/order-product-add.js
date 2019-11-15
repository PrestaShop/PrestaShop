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
    this.quantityInput = $(OrderViewPageMap.productAddQuantityInput);
    this.availableText = $(OrderViewPageMap.productAddAvailableText);
    this.available = null;
    this.setupListener();
    this.product = {};
  }

  setupListener() {
    this.quantityInput.on('change keyup', (event) => {
      if (this.available === null) {
        return;
      }
      const quantity = parseInt(event.target.value ? event.target.value : 0);
      const available = this.available - quantity;
      this.availableText.text(available);
      this.availableText.toggleClass('text-danger font-weight-bold', available < 0);
    });
    this.productIdInput.on('change', () => {
      this.productAddActionBtn.removeAttr('disabled');
    });
    this.priceTaxIncludedInput.on('change keyup', (event) => { this.priceTaxExcludedInput.val(event.target.value); });
    this.priceTaxExcludedInput.on('change keyup', (event) => { this.priceTaxIncludedInput.val(event.target.value); });
    this.productAddActionBtn.on('click', event => this.addProduct($(event.currentTarget).data('order-id')));
  }

  setProduct(product) {
    this.productIdInput.val(product.product_id).trigger('change');
    this.priceTaxExcludedInput.val(product.price_tax_excl);
    this.priceTaxIncludedInput.val(product.price_tax_incl);
    this.available = product.stock;
    this.quantityInput.trigger('change');
    this.setCombinations(product.combinations);
  }

  setCombinations(combinations) {
    this.combinationsSelect.empty();
    Object.entries(combinations).forEach((val) => {
      this.combinationsSelect.append(`<option value="${val[1].attribute_combination_id}">${val[1].attribute}</option>`);
    });
    this.combinationsBlock.toggleClass('d-none', Object.keys(combinations).length === 0);
  }

  addProduct(orderId) {
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
    });
  }
}
