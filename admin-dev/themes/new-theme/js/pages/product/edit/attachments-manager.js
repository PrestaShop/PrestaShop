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

import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';

const {$} = window;

export default class AttachmentsManager {
  constructor() {
    this.$attachmentsContainer = $(ProductMap.attachments.attachmentsContainer);
    this.$attachmentsCollection = $(ProductMap.attachments.attachmentsCollection);
    this.prototypeTemplate = this.$attachmentsCollection.data('prototype');
    this.prototypeName = this.$attachmentsCollection.data('prototypeName');
    this.eventEmitter = window.prestashop.instance.eventEmitter;

    this.init();
  }

  /**
   * @private
   */
  init() {
    this.initAddAttachmentIframe();
    this.$attachmentsContainer.on('click', ProductMap.attachments.removeAttachmentBtn, (e) => {
      this.removeAttachmentRow(e);
    });
  }

  /**
   * @private
   */
  initAddAttachmentIframe() {
    this.$attachmentsContainer.find(ProductMap.attachments.addAttachmentBtn).fancybox({
      type: 'iframe',
      width: '90%',
      height: '90%',
      onUpdate: () => {
        //@todo: get recently added attachment data and put it in parent element (or local storage?)
      }
    });
  }

  /**
   * @param {Object} event
   *
   * @private
   */
  removeAttachmentRow(event) {
    const $removeButton = $(event.currentTarget);
    const $thisRow = $removeButton.closest(ProductMap.attachments.attachedFileRow);
    const attachmentId = $thisRow.find(ProductMap.attachments.attachmentIdInputs).val();

    $thisRow.remove();
    this.eventEmitter.emit(ProductEventMap.attachments.rowRemoved, {attachmentId});
    this.refreshState();
  }

  /**
   * @private
   */
  refreshState() {
    const $attachmentsCollection = this.$attachmentsCollection;
    const isEmpty = $attachmentsCollection.find(ProductMap.attachments.attachedFileRow).length === 0;

    const $emptyState = $(ProductMap.attachments.emptyState);

    if (isEmpty) {
      $emptyState.removeClass('d-none');
      $attachmentsCollection.addClass('d-none');
    } else {
      $emptyState.addClass('d-none');
      $attachmentsCollection.removeClass('d-none');
    }
  }

  // /**
  //  * @private
  //  * @todo: use after new file is added
  //  */
  // addAttachmentRow() {
  //   this.$attachmentsCollection.empty();
  //
  //   let rowIndex = 0;
  //   data.productAttachments.forEach((attachment) => {
  //     //@todo; add other fields besides id so they can be filled with values
  //     // const $row = $(this.getPrototypeRow(rowIndex));
  //     // const $attachmentIdInput = $(ProductMap.attachments.collectionRow.attachmentIdInput(rowIndex), $row);
  //     const $attachmentIdInput = $(this.getPrototypeRow(rowIndex));
  //     $attachmentIdInput.val(attachment.id);
  //     // this.$attachmentsCollection.append($row);
  //     this.$attachmentsCollection.append($attachmentIdInput);
  //
  //     rowIndex += 1;
  //   });
  // }

  /**
   * @param {Number} rowIndex
   *
   * @returns {String}
   *
   * @private
   */
  getPrototypeRow(rowIndex) {
    return this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), rowIndex);
  }
}
