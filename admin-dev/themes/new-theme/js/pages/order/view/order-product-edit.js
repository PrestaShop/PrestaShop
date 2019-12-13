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

export default class OrderProductEdit {
  constructor(orderProductId) {
    this.router = new Router();
    this.orderProductId = orderProductId;
    this.productRow = $(`#orderProduct_${this.orderProductId}`);
    this.productEditActionBtn = $(OrderViewPageMap.productEditActionBtn);
    this.productIdInput = $(OrderViewPageMap.productEditOrderDetailInput);
    this.priceTaxIncludedInput = $(OrderViewPageMap.productEditPriceTaxInclInput);
    this.priceTaxExcludedInput = $(OrderViewPageMap.productEditPriceTaxExclInput);
    this.taxRateInput = $(OrderViewPageMap.productEditTaxRateInput);
    this.quantityInput = $(OrderViewPageMap.productEditQuantityInput);
    this.locationText = $(OrderViewPageMap.productEditLocationText);
    this.availableText = $(OrderViewPageMap.productEditAvailableText);
    this.priceTotalText = $(OrderViewPageMap.productEditTotalPriceText);
    this.product = {};
  }

  setupListener() {
    this.productRowEdit.find(OrderViewPageMap.productEditQuantityInput).on('change keyup', (event) => {
      const priceTaxCalculator = new OrderPrices();
      const quantity = parseInt(event.target.value ? event.target.value : 0, 10);
      const taxIncluded = parseFloat(this.productRowEdit.find(OrderViewPageMap.productEditPriceTaxInclInput).val());
      this.productRowEdit.find(OrderViewPageMap.productEditTotalPriceText).html(
        priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded)
      );
    });
    this.productRowEdit.find(OrderViewPageMap.productEditPriceTaxInclInput).on('change keyup', (event) => {
      const priceTaxCalculator = new OrderPrices();
      const quantity = parseInt(this.productRowEdit.find(OrderViewPageMap.productEditQuantityInput).val(), 10);
      const taxIncluded = parseFloat(event.target.value);
      const taxExcluded = priceTaxCalculator.calculateTaxExcluded(
        taxIncluded,
        this.productRowEdit.find(OrderViewPageMap.productEditTaxRateInput).val()
      );
      this.productRowEdit.find(OrderViewPageMap.productEditPriceTaxExclInput).val(taxExcluded);
      this.productRowEdit.find(OrderViewPageMap.productEditTotalPriceText).html(
        priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded)
      );
    });
    this.productRowEdit.find(OrderViewPageMap.productEditPriceTaxExclInput).on('change keyup', (event) => {
      const priceTaxCalculator = new OrderPrices();
      const quantity = parseInt(this.productRowEdit.find(OrderViewPageMap.productEditQuantityInput).val(), 10);
      const taxExcluded = parseFloat(event.target.value);
      const taxIncluded = priceTaxCalculator.calculateTaxIncluded(
        taxExcluded,
        this.productRowEdit.find(OrderViewPageMap.productEditTaxRateInput).val()
      );
      this.productRowEdit.find(OrderViewPageMap.productEditPriceTaxInclInput).val(taxIncluded);
      this.productRowEdit.find(OrderViewPageMap.productEditTotalPriceText).html(
        priceTaxCalculator.calculateTotalPrice(quantity, taxIncluded)
      );
    });
    this.productRowEdit.find(OrderViewPageMap.productEditActionBtn).on('click', (event) => {
      const $btn = $(event.currentTarget);
      const confirmed = confirm($btn.data('updateMessage'));
      if (!confirmed) {
        return;
      }
      $btn.attr('disabled', 'disabled');
      this.editProduct(
        $(event.currentTarget).data('orderId'),
        $(event.currentTarget).data('orderDetailId'),
      );
    });
  }

  displayProduct(product) {
    const priceTaxCalculator = new OrderPrices();
    const $productEditRow = $(OrderViewPageMap.productEditRow).clone(true);
    $productEditRow.find('td:nth-child(1)').html(this.productRow.find('td:nth-child(1)').html());
    $productEditRow.find('td:nth-child(2)').html(this.productRow.find('td:nth-child(2)').html());
    $productEditRow.find(OrderViewPageMap.productEditOrderDetailInput).val(this.orderProductId);
    $productEditRow.find(OrderViewPageMap.productEditPriceTaxExclInput).val(product.price_tax_excl);
    $productEditRow.find(OrderViewPageMap.productEditPriceTaxInclInput).val(product.price_tax_incl);
    $productEditRow.find(OrderViewPageMap.productEditQuantityInput).val(product.quantity);
    $productEditRow.find(OrderViewPageMap.productEditTaxRateInput).val(product.tax_rate);
    $productEditRow.find(OrderViewPageMap.productEditLocationText).html(product.location);
    $productEditRow.find(OrderViewPageMap.productEditAvailableText).html(product.availableQuantity);
    $productEditRow.find(OrderViewPageMap.productEditTotalPriceText).html(priceTaxCalculator.calculateTotalPrice(
      product.quantity,
      product.price_tax_excl
    ));
    $productEditRow.find(OrderViewPageMap.productEditActionBtn).data('orderDetailId', this.orderProductId);
    $productEditRow.find(OrderViewPageMap.productCancelEditBtn).data('orderDetailId', this.orderProductId);
    $productEditRow.attr('id', `orderProduct_${this.orderProductId}_edit`);
    this.productRow.addClass('d-none').after($productEditRow.removeClass('d-none'));

    this.productRowEdit = $(`#orderProduct_${this.orderProductId}_edit`);
    this.setupListener();
  }

  editProduct(orderId, orderDetailId) {
    const params = {
      price_tax_incl: this.productRowEdit.find(OrderViewPageMap.productEditPriceTaxInclInput).val(),
      price_tax_excl: this.productRowEdit.find(OrderViewPageMap.productEditPriceTaxInclInput).val(),
      quantity: this.productRowEdit.find(OrderViewPageMap.productEditQuantityInput).val(),
    };
    $.ajax({
      url: this.router.generate('admin_orders_update_product', {orderId, orderDetailId}),
      method: 'POST',
      data: params,
    }).then((response) => {
      EventEmitter.emit(OrderViewEventMap.productEditedToOrder, {
        orderId,
        orderDetailId,
        newRow: response
      });
    }, (response) => {
      if (response.message) {
        $.growl.error({message: response.message});
      }
    });
  }
}
