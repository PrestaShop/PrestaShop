/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';

const {$} = window;

export default class OrderPricesRefresher {
  router: Router;

  constructor() {
    this.router = new Router();
  }

  refresh(orderId: number): void {
    $.getJSON(
      this.router.generate('admin_orders_get_prices', {orderId}),
    ).then((response) => {
      $(OrderViewPageMap.orderTotal).text(response.orderTotalFormatted);
      $(OrderViewPageMap.orderDiscountsTotal).text(
        `-${response.discountsAmountFormatted}`,
      );
      $(OrderViewPageMap.orderDiscountsTotalContainer).toggleClass(
        'd-none',
        !response.discountsAmountDisplayed,
      );
      $(OrderViewPageMap.orderProductsTotal).text(
        response.productsTotalFormatted,
      );
      $(OrderViewPageMap.orderShippingTotal).text(
        response.shippingTotalFormatted,
      );
      $(OrderViewPageMap.orderShippingTotalContainer).toggleClass(
        'd-none',
        !response.shippingTotalDisplayed,
      );
      $(OrderViewPageMap.orderTaxesTotal).text(response.taxesTotalFormatted);
    });
  }

  refreshProductPrices(orderId: number): void {
    $.getJSON(
      this.router.generate('admin_orders_product_prices', {orderId}),
    ).then((productPricesList) => {
      productPricesList.forEach((productPrices: Record<string, any>) => {
        const orderProductTrId = OrderViewPageMap.productsTableRow(
          productPrices.orderDetailId,
        );
        let $quantity = $(productPrices.quantity);

        if (productPrices.quantity > 1) {
          $quantity = $quantity.wrap(
            '<span class="badge badge-secondary rounded-circle"></span>',
          );
        }

        $(`${orderProductTrId} ${OrderViewPageMap.productEditUnitPrice}`).text(
          productPrices.unitPrice,
        );
        $(`${orderProductTrId} ${OrderViewPageMap.productEditQuantity}`).html(
          $quantity.html(),
        );
        $(
          `${orderProductTrId} ${OrderViewPageMap.productEditAvailableQuantity}`,
        ).text(productPrices.availableQuantity);
        $(`${orderProductTrId} ${OrderViewPageMap.productEditTotalPrice}`).text(
          productPrices.totalPrice,
        );

        // update order row price values
        const productEditButton = $(
          OrderViewPageMap.productEditBtn(productPrices.orderDetailId),
        );

        productEditButton.data(
          'product-price-tax-incl',
          productPrices.unitPriceTaxInclRaw,
        );
        productEditButton.data(
          'product-price-tax-excl',
          productPrices.unitPriceTaxExclRaw,
        );
        productEditButton.data('product-quantity', productPrices.quantity);
      });
    });
  }

  /**
   * This method will check if the same product is already present in the order
   * and if so and if the price of the 2 products doesn't match will return either
   * 'invoice' if the 2 products are in 2 different invoices or 'product' if the 2 products
   * are in the same invoice (or no invoice yet). Only products that have different customizations
   * can be twice in a same invoice.
   * Will return null if no matching products are found.
   */
  checkOtherProductPricesMatch(
    givenPrice: number,
    productId: number,
    combinationId: number,
    invoiceId: number,
    orderDetailId?: number,
  ): null | string {
    const productRows = document.querySelectorAll('tr.cellProduct');
    // We convert the expected values into int/float to avoid a type mismatch that would be wrongly interpreted
    const expectedProductId = Number(productId);
    const expectedCombinationId = Number(combinationId);
    const expectedGivenPrice = Number(givenPrice);
    let unmatchingInvoicePriceExists = false;
    let unmatchingProductPriceExists = false;

    productRows.forEach((productRow) => {
      const productRowId = $(productRow).attr('id');

      // No need to check edited row (especially if it's the only one for this product)
      if (orderDetailId && productRowId === `orderProduct_${orderDetailId}`) {
        return;
      }

      const productEditBtn = $(
        `#${productRowId} ${OrderViewPageMap.productEditButtons}`,
      );
      const currentOrderInvoiceId = Number(
        productEditBtn.data('order-invoice-id'),
      );

      const currentProductId = Number(productEditBtn.data('product-id'));
      const currentCombinationId = Number(
        productEditBtn.data('combination-id'),
      );

      if (
        currentProductId !== expectedProductId
        || currentCombinationId !== expectedCombinationId
      ) {
        return;
      }

      if (
        expectedGivenPrice
        !== Number(productEditBtn.data('product-price-tax-incl'))
      ) {
        if (
          !invoiceId
          || (invoiceId
            && currentOrderInvoiceId
            && invoiceId === currentOrderInvoiceId)
        ) {
          unmatchingProductPriceExists = true;
        } else {
          unmatchingInvoicePriceExists = true;
        }
      }
    });

    if (unmatchingInvoicePriceExists) {
      return 'invoice';
    }

    if (unmatchingProductPriceExists) {
      return 'product';
    }

    return null;
  }
}
