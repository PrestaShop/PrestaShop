/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import OrderViewPageMap from '../OrderViewPageMap';

const {$} = window;

/**
 * All actions for order view page messages are registered in this class.
 */
export default class OrderViewPageMessagesHandler {
  $orderMessageChangeWarning: JQuery;

  $messagesContainer: JQuery;

  constructor() {
    this.$orderMessageChangeWarning = $(OrderViewPageMap.orderMessageChangeWarning);
    this.$messagesContainer = $(OrderViewPageMap.orderMessagesContainer);
  }

  listenForPredefinedMessageSelection(): void {
    this.handlePredefinedMessageSelection();
  }

  listenForFullMessagesOpen(): void {
    this.onFullMessagesOpen();
  }

  /**
   * Handles predefined order message selection.
   *
   * @private
   */
  private handlePredefinedMessageSelection(): void {
    $(document).on('change', OrderViewPageMap.orderMessageNameSelect, (e) => {
      const $currentItem = $(e.currentTarget);
      const valueId = $currentItem.val();

      if (!valueId) {
        return;
      }

      const message = this.$messagesContainer.find(`div[data-id=${valueId}]`).text().trim();
      const $orderMessage = $(OrderViewPageMap.orderMessage);
      const orderMessageValue = <string>$orderMessage.val();
      const isSameMessage = orderMessageValue?.trim() === message;

      if (isSameMessage) {
        return;
      }

      if ($orderMessage.val() && !window.confirm(this.$orderMessageChangeWarning.text())) {
        return;
      }

      $orderMessage.val(message);
      $orderMessage.trigger('input');
    });
  }

  /**
   * Listens for event when all messages modal is being opened
   *
   * @private
   */
  private onFullMessagesOpen(): void {
    $(document).on('click', OrderViewPageMap.openAllMessagesBtn, () => this.scrollToMsgListBottom());
  }

  /**
   * Scrolls down to the bottom of all messages list
   *
   * @private
   */
  private scrollToMsgListBottom(): void {
    const $msgModal = $(OrderViewPageMap.allMessagesModal);
    const msgList = document.querySelector(OrderViewPageMap.allMessagesList);

    const classCheckInterval = window.setInterval(() => {
      if ($msgModal.hasClass('show') && msgList) {
        msgList.scrollTop = <number>msgList?.scrollHeight;
        clearInterval(classCheckInterval);
      }
    }, 10);
  }
}
