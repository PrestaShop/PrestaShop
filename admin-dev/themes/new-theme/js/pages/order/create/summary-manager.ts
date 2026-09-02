/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {EventEmitter} from '@components/event-emitter';
import Router from '@components/router';
import eventMap from './event-map';
import SummaryRenderer from './summary-renderer';

const {$} = window;

/**
 * Manages summary block
 */
export default class SummaryManager {
  router: Router;

  summaryRenderer: SummaryRenderer;

  constructor() {
    this.router = new Router();
    this.summaryRenderer = new SummaryRenderer();
    this.initListeners();
  }

  /**
   * Inits event listeners
   *
   * @private
   */
  private initListeners(): void {
    this.onProcessOrderEmailError();
    this.onProcessOrderEmailSuccess();
  }

  /**
   * Listens for process order email sending success event
   *
   * @private
   */
  private onProcessOrderEmailSuccess(): void {
    EventEmitter.on(eventMap.processOrderEmailSent, (response) => {
      this.summaryRenderer.cleanAlerts();
      this.summaryRenderer.renderSuccessMessage(response.message);
    });
  }

  /**
   * Listens for process order email failed event
   *
   * @private
   */
  private onProcessOrderEmailError(): void {
    EventEmitter.on(eventMap.processOrderEmailFailed, (response) => {
      this.summaryRenderer.cleanAlerts();
      this.summaryRenderer.renderErrorMessage(response.responseJSON.message);
    });
  }

  /**
   * Sends email to customer with link of order processing
   *
   * @param {Number} cartId
   */
  sendProcessOrderEmail(cartId: number): void {
    $.post(this.router.generate('admin_orders_send_process_order_email'), {
      cartId,
    })
      .then((response) => EventEmitter.emit(eventMap.processOrderEmailSent, response),
      )
      .catch((e) => {
        EventEmitter.emit(eventMap.processOrderEmailFailed, e);
      });
  }
}
