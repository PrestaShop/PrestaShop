/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';

const {$} = window;

export default class OrderInvoicesRefresher {
  router: Router;

  constructor() {
    this.router = new Router();
  }

  refresh(orderId: number): void {
    $.getJSON(this.router.generate('admin_orders_get_invoices', {orderId}))
      .then((response) => {
        if (!response || !response.invoices || Object.keys(response.invoices).length <= 0) {
          return;
        }

        const $paymentInvoiceSelect = $(OrderViewPageMap.orderPaymentInvoiceSelect);
        const $addProductInvoiceSelect = $(OrderViewPageMap.productAddInvoiceSelect);
        const $existingInvoicesGroup = $addProductInvoiceSelect.find('optgroup:first');
        const $productEditInvoiceSelect = $(OrderViewPageMap.productEditInvoiceSelect);
        const $addDiscountInvoiceSelect = $(OrderViewPageMap.addCartRuleInvoiceIdSelect);
        $existingInvoicesGroup.empty();
        $paymentInvoiceSelect.empty();
        $productEditInvoiceSelect.empty();
        $addDiscountInvoiceSelect.empty();

        Object.keys(response.invoices).forEach((invoiceName) => {
          const invoiceId = response.invoices[invoiceName];
          const invoiceNameWithoutPrice = invoiceName.split(' - ')[0];

          $existingInvoicesGroup.append(`<option value="${invoiceId}">${invoiceNameWithoutPrice}</option>`);
          $paymentInvoiceSelect.append(`<option value="${invoiceId}">${invoiceNameWithoutPrice}</option>`);
          $productEditInvoiceSelect.append(`<option value="${invoiceId}">${invoiceNameWithoutPrice}</option>`);
          $addDiscountInvoiceSelect.append(`<option value="${invoiceId}">${invoiceName}</option>`);
        });

        const productAddSelect = <HTMLSelectElement>document.querySelector(OrderViewPageMap.productAddInvoiceSelect);

        if (productAddSelect) {
          productAddSelect.selectedIndex = 0;
        }
      });
  }
}
