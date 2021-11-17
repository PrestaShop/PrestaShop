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

import OrderViewPageMap from '../OrderViewPageMap';

const {$} = window;

/**
 * All actions for order view page messages are registered in this class.
 */
export default class OrderViewPageMessagesHandler {
  constructor() {
    this.$orderMessageChangeWarning = $(OrderViewPageMap.orderMessageChangeWarning);
    this.$messagesContainer = $(OrderViewPageMap.orderMessagesContainer);

    return {
      listenForPredefinedMessageSelection: () => this.handlePredefinedMessageSelection(),
      listenForFullMessagesOpen: () => this.onFullMessagesOpen(),
    };
  }

  /**
   * Handles predefined order message selection.
   *
   * @private
   */
  handlePredefinedMessageSelection() {
    $(document).on('change', OrderViewPageMap.orderMessageNameSelect, (e) => {
      const $currentItem = $(e.currentTarget);
      const valueId = $currentItem.val();

      if (!valueId) {
        return;
      }

      const message = this.$messagesContainer.find(`div[data-id=${valueId}]`).text().trim();
      const $orderMessage = $(OrderViewPageMap.orderMessage);
      const isSameMessage = $orderMessage.val().trim() === message;

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
  onFullMessagesOpen() {
    $(document).on('click', OrderViewPageMap.openAllMessagesBtn, () => this.scrollToMsgListBottom());
  }

  /**
   * Scrolls down to the bottom of all messages list
   *
   * @private
   */
  scrollToMsgListBottom() {
    const $msgModal = $(OrderViewPageMap.allMessagesModal);
    const msgList = document.querySelector(OrderViewPageMap.allMessagesList);

    const classCheckInterval = window.setInterval(() => {
      if ($msgModal.hasClass('show')) {
        msgList.scrollTop = msgList.scrollHeight;
        clearInterval(classCheckInterval);
      }
    }, 10);
  }
}
