/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

export default class ImportProgressModal {
  /**
   * Show the import progress modal window.
   */
  show() {
    this.progressModal.modal('show');
  }

  /**
   * Hide the import progress modal window.
   */
  hide() {
    this.progressModal.modal('hide');
  }

  /**
   * Updates the import progressbar.
   *
   * @param {int} percentage
   */
  updateProgress(percentage) {
    let $progressBar = this.progressBar;

    percentage = parseInt(percentage);

    $progressBar.css('width', percentage + '%');
    $progressBar.find('> span').text(percentage + ' %');
  }

  /**
   * Shows information messages in the import modal.
   *
   * @param {Array} messages
   */
  showInfoMessages(messages) {
    this._showMessages(this.infoMessageBlock, messages);
  }

  /**
   * Shows warning messages in the import modal.
   *
   * @param {Array} messages
   */
  showWarningMessages(messages) {
    this._showMessages(this.warningMessageBlock, messages);
  }

  /**
   * Shows error messages in the import modal.
   *
   * @param {Array} messages
   */
  showErrorMessages(messages) {
    this._showMessages(this.errorMessageBlock, messages);
  }

  /**
   * Show messages in given message block.
   *
   * @param {jQuery} $messageBlock
   * @param {Array} messages
   * @private
   */
  _showMessages($messageBlock, messages) {
    let showMessagesBlock = false;

    for (let key in messages) {
      // Indicate that the messages block should be displayed
      showMessagesBlock = true;

      let message = $('<div>');
      message.text(messages[key]);
      message.addClass('message');

      $messageBlock.append(message);
    }

    if (showMessagesBlock) {
      $messageBlock.removeClass('d-none');
    }
  }

  /**
   * Reset the modal - resets progress bar and removes messages.
   */
  reset() {
    this.updateProgress(0);
    this.infoMessageBlock.addClass('d-none').find('.message').remove();
  }

  /**
   * Gets import progress modal.
   *
   * @returns {jQuery}
   */
  get progressModal() {
    return $('#import_progress_modal');
  }

  /**
   * Gets import progress bar.
   *
   * @returns {jQuery}
   */
  get progressBar() {
    return this.progressModal.find('.progress-bar');
  }

  /**
   * Gets information messages block.
   *
   * @returns {jQuery|HTMLElement}
   */
  get infoMessageBlock() {
    return $('.js-import-info');
  }

  /**
   * Gets error messages block.
   *
   * @returns {jQuery|HTMLElement}
   */
  get errorMessageBlock() {
    return $('.js-import-errors');
  }

  /**
   * Gets warning messages block.
   *
   * @returns {jQuery|HTMLElement}
   */
  get warningMessageBlock() {
    return $('.js-import-warnings');
  }

  /**
   * Gets post limit message.
   *
   * @returns {jQuery|HTMLElement}
   */
  get postLimitMessage() {
    return $('.js-post-limit-warning');
  }
}
