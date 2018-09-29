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
   * @param {number} completed number of completed items.
   * @param {number} total number of items in total.
   */
  updateProgress(completed, total) {
    completed = parseInt(completed);
    total = parseInt(total);

    let $progressBar = this.progressBar,
        percentage = completed / total * 100;

    $progressBar.css('width', percentage + '%');
    $progressBar.find('> span').text(completed + '/' + total);
  }

  /**
   * Updates the progress bar label.
   *
   * @param {String} label if not provided - will use the default label
   */
  updateProgressLabel(label) {
    this.progressLabel.text(label);
  }

  setImportingProgressLabel() {
    this.updateProgressLabel(this.progressModal.find('.modal-body').data('importing-label'));
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
   * Shows the import success message.
   */
  showSuccessMessage() {
    this.successMessageBlock.removeClass('d-none');
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
   * Show the "Ignore warnings" button.
   */
  showIgnoreWarningsButton() {
    this.ignoreWarningsButton.removeClass('d-none')
  }

  /**
   * Show the "Abort import" button.
   */
  showAbortImportButton() {
    this.abortImportButton.removeClass('d-none');
  }

  /**
   * Reset the modal - resets progress bar and removes messages.
   */
  reset() {
    this.updateProgress(0, 0);
    this.updateProgressLabel(this.progressLabel.attr('default-value'));
    this.ignoreWarningsButton.addClass('d-none');
    this.abortImportButton.addClass('d-none');
    this.successMessageBlock.addClass('d-none');
    this.infoMessageBlock.addClass('d-none').find('.message').remove();
    this.errorMessageBlock.addClass('d-none').find('.message').remove();
    this.warningMessageBlock.addClass('d-none').find('.message').remove();
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
   * Gets success messages block.
   *
   * @returns {jQuery|HTMLElement}
   */
  get successMessageBlock() {
    return $('.js-import-success');
  }

  /**
   * Gets post limit message.
   *
   * @returns {jQuery|HTMLElement}
   */
  get postLimitMessage() {
    return $('.js-post-limit-warning');
  }

  /**
   * Gets "Ignore warnings" button.
   *
   * @returns {jQuery|HTMLElement}
   */
  get ignoreWarningsButton() {
    return $('.js-ignore-warnings');
  }

  /**
   * Gets "Abort import" button.
   *
   * @returns {jQuery|HTMLElement}
   */
  get abortImportButton() {
    return $('.js-abort-import');
  }

  /**
   * Gets progress bar label.
   *
   * @returns {jQuery|HTMLElement}
   */
  get progressLabel() {
    return $('#import_progress_bar').find('.progress-details-text');
  }
}
