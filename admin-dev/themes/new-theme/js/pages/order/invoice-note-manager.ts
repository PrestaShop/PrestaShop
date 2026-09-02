/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import OrderViewPageMap from './OrderViewPageMap';

const {$} = window;

/**
 * Manages adding/editing note for invoice documents.
 */
export default class InvoiceNoteManager {
  constructor() {
    this.setupListeners();
  }

  setupListeners(): void {
    this.initShowNoteFormEventHandler();
    this.initCloseNoteFormEventHandler();
    this.initEnterPaymentEventHandler();
  }

  initShowNoteFormEventHandler(): void {
    $('.js-open-invoice-note-btn').on('click', (event) => {
      event.preventDefault();
      const $btn = $(event.currentTarget);
      const $noteRow = $btn.closest('tr').next();

      $noteRow.removeClass('d-none');
    });
  }

  initCloseNoteFormEventHandler(): void {
    $('.js-cancel-invoice-note-btn').on('click', (event) => {
      $(event.currentTarget).closest('tr').addClass('d-none');
    });
  }

  initEnterPaymentEventHandler(): void {
    $('.js-enter-payment-btn').on('click', (event) => {
      const $btn = $(event.currentTarget);
      const paymentAmount = $btn.data('payment-amount');

      $(OrderViewPageMap.viewOrderPaymentsBlock).get(0)?.scrollIntoView({behavior: 'smooth'});
      $(OrderViewPageMap.orderPaymentFormAmountInput).val(paymentAmount);
    });
  }
}
