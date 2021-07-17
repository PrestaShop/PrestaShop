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

const {$} = window;

export default class OrderPricesRefresher {
  constructor() {
    this.router = new Router();
  }

  refresh(orderId) {
    $.getJSON(this.router.generate('admin_orders_get_prices', {orderId})).then(response => {
      $(OrderViewPageMap.orderTotal).text(response.orderTotalFormatted);
      $(OrderViewPageMap.orderDiscountsTotal).text(`-${response.discountsAmountFormatted}`);
      $(OrderViewPageMap.orderDiscountsTotalContainer).toggleClass('d-none', !response.discountsAmountDisplayed);
      $(OrderViewPageMap.orderProductsTotal).text(response.productsTotalFormatted);
      $(OrderViewPageMap.orderShippingTotal).text(response.shippingTotalFormatted);
      $(OrderViewPageMap.orderShippingTotalContainer).toggleClass('d-none', !response.shippingTotalDisplayed);
      $(OrderViewPageMap.orderTaxesTotal).text(response.taxesTotalFormatted);
    });
  }

  refreshProductPrices(orderId) {
    $.getJSON(this.router.generate('admin_orders_product_prices', {orderId})).then(productPricesList => {
      productPricesList.forEach(productPrices => {
        const orderProductTrId = OrderViewPageMap.productsTableRow(productPrices.orderDetailId);
        let $quantity = $(productPrices.quantity);
        if (productPrices.quantity > 1) {
          $quantity = $quantity.wrap('<span class="badge badge-secondary rounded-circle"></span>');
        }

        $(`${orderProductTrId} ${OrderViewPageMap.productEditUnitPrice}`).text(productPrices.unitPrice);
        $(`${orderProductTrId} ${OrderViewPageMap.productEditQuantity}`).html($quantity.html());
        $(`${orderProductTrId} ${OrderViewPageMap.productEditAvailableQuantity}`).text(productPrices.availableQuantity);
        $(`${orderProductTrId} ${OrderViewPageMap.productEditTotalPrice}`).text(productPrices.totalPrice);

        // update order row price values
        const productEditButton = $(OrderViewPageMap.productEditBtn(productPrices.orderDetailId));

        productEditButton.data('product-price-tax-incl', productPrices.unitPriceTaxInclRaw);
        productEditButton.data('product-price-tax-excl', productPrices.unitPriceTaxExclRaw);
        productEditButton.data('product-quantity', productPrices.quantity);
      });
    });
  }

  checkOtherProductPricesMatch(givenPrice, productId, combinationId, invoiceId, orderDetailId) {
    const productRows = document.querySelectorAll('tr.cellProduct');
    // We convert the expected values into int/float to avoid a type mismatch that would be wrongly interpreted
    const expectedProductId = Number(productId);
    const expectedCombinationId = Number(combinationId);
    const expectedGivenPrice = Number(givenPrice);
    let unmatchingPriceExists = false;

    productRows.forEach((productRow) => {
      const productRowId = $(productRow).attr('id');

      // No need to check edited row (especially if it's the only one for this product)
      if (orderDetailId && productRowId === `orderProduct_${orderDetailId}`) {
        return;
      }

      const productEditBtn = $(`#${productRowId} ${OrderViewPageMap.productEditButtons}`);
      const currentOrderInvoiceId = Number(productEditBtn.data('order-invoice-id'));

      // No need to check target invoice, only if others have matching products
      if (invoiceId && currentOrderInvoiceId && invoiceId === currentOrderInvoiceId) {
        return;
      }

      const currentProductId = Number(productEditBtn.data('product-id'));
      const currentCombinationId = Number(productEditBtn.data('combination-id'));

      if (currentProductId !== expectedProductId || currentCombinationId !== expectedCombinationId) {
        return;
      }

      if (expectedGivenPrice !== Number(productEditBtn.data('product-price-tax-incl'))) {
        unmatchingPriceExists = true;
      }
    });

    return !unmatchingPriceExists;
  }
}
