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
    const progressCompleted = parseInt(completed, 10);
    const progressTotal = parseInt(total, 10);
    const $progressBar = this.progressBar;
    const percentage = (progressCompleted / progressTotal) * 100;

    $progressBar.css('width', `${percentage}%`);
    $progressBar.find('> span').text(`${progressCompleted}/${progressTotal}`);
  }

  /**
   * Updates the progress bar label.
   *
   * @param {String} label if not provided - will use the default label
   */
  updateProgressLabel(label) {
    this.progressLabel.text(label);
  }

  /**
   * Sets the progress label to "importing"
   */
  setImportingProgressLabel() {
    this.updateProgressLabel(this.progressModal.find('.modal-body').data('importing-label'));
  }

  /**
   * Sets the progress label to "imported"
   */
  setImportedProgressLabel() {
    this.updateProgressLabel(this.progressModal.find('.modal-body').data('imported-label'));
  }

  /**
   * Shows information messages in the import modal.
   *
   * @param {Array} messages
   */
  showInfoMessages(messages) {
    this.showMessages(this.infoMessageBlock, messages);
  }

  /**
   * Shows warning messages in the import modal.
   *
   * @param {Array} messages
   */
  showWarningMessages(messages) {
    this.showMessages(this.warningMessageBlock, messages);
  }

  /**
   * Shows error messages in the import modal.
   *
   * @param {Array} messages
   */
  showErrorMessages(messages) {
    this.showMessages(this.errorMessageBlock, messages);
  }

  /**
   * Shows the import success message.
   */
  showSuccessMessage() {
    this.successMessageBlock.removeClass('d-none');
  }

  /**
   * Shows the post size limit warning message.
   *
   * @param {number} postSizeValue to be shown in the warning
   */
  showPostLimitMessage(postSizeValue) {
    this.postLimitMessage.find('#post_limit_value').text(postSizeValue);
    this.postLimitMessage.removeClass('d-none');
  }

  /**
   * Show messages in given message block.
   *
   * @param {jQuery} $messageBlock
   * @param {Array} messages
   * @private
   */
  showMessages($messageBlock, messages) {
    let showMessagesBlock = false;

    Object.values(messages).forEach((msg) => {
      // Indicate that the messages block should be displayed
      showMessagesBlock = true;

      const message = $('<div>');
      message.text(msg);
      message.addClass('message');

      $messageBlock.append(message);
    });

    if (showMessagesBlock) {
      $messageBlock.removeClass('d-none');
    }
  }

  /**
   * Show the "Ignore warnings and continue" button.
   */
  showContinueImportButton() {
    this.continueImportButton.removeClass('d-none');
  }

  /**
   * Hide the "Ignore warnings and continue" button.
   */
  hideContinueImportButton() {
    this.continueImportButton.addClass('d-none');
  }

  /**
   * Show the "Abort import" button.
   */
  showAbortImportButton() {
    this.abortImportButton.removeClass('d-none');
  }

  /**
   * Hide the "Abort import" button.
   */
  hideAbortImportButton() {
    this.abortImportButton.addClass('d-none');
  }

  /**
   * Show the "Close" button of the modal.
   */
  showCloseModalButton() {
    this.closeModalButton.removeClass('d-none');
  }

  /**
   * Hide the "Close" button.
   */
  hideCloseModalButton() {
    this.closeModalButton.addClass('d-none');
  }

  /**
   * Clears all warning messages from the modal.
   */
  clearWarningMessages() {
    this.warningMessageBlock.addClass('d-none').find('.message').remove();
  }

  /**
   * Reset the modal - resets progress bar and removes messages.
   */
  reset() {
    // Reset the progress bar
    this.updateProgress(0, 0);
    this.updateProgressLabel(this.progressLabel.attr('default-value'));

    // Hide action buttons
    this.continueImportButton.addClass('d-none');
    this.abortImportButton.addClass('d-none');
    this.closeModalButton.addClass('d-none');

    // Remove messages
    this.successMessageBlock.addClass('d-none');
    this.infoMessageBlock.addClass('d-none').find('.message').remove();
    this.errorMessageBlock.addClass('d-none').find('.message').remove();
    this.postLimitMessage.addClass('d-none');
    this.clearWarningMessages();
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
   * Gets "Ignore warnings and continue" button.
   *
   * @returns {jQuery|HTMLElement}
   */
  get continueImportButton() {
    return $('.js-continue-import');
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
   * Gets "Close" button of the modal.
   *
   * @returns {jQuery|HTMLElement}
   */
  get closeModalButton() {
    return $('.js-close-modal');
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
