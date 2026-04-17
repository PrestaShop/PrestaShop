/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';
import InvoiceNoteManager from '../invoice-note-manager';

const {$} = window;

export default class OrderDocumentsRefresher {
  router: Router;

  invoiceNoteManager: InvoiceNoteManager;

  constructor() {
    this.router = new Router();
    this.invoiceNoteManager = new InvoiceNoteManager();
  }

  refresh(orderId: number): void {
    $.getJSON(this.router.generate('admin_orders_get_documents', {orderId}))
      .then((response) => {
        $(OrderViewPageMap.orderDocumentsTabCount).text(response.total);
        $(OrderViewPageMap.orderDocumentsTabBody).html(response.html);
        this.invoiceNoteManager.setupListeners();
      });
  }
}
