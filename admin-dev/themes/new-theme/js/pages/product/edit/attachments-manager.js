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
import Router from '@components/router';
import {createAttachment, getAttachmentInfo} from '@pages/product/services/attachments-service';

const {$} = window;

export default class AttachmentsManager {
  constructor() {
    this.$attachmentsContainer = $(ProductMap.attachments.attachmentsContainer);
    this.$attachmentsCollection = $(ProductMap.attachments.attachmentsCollection);
    this.$attachmentsTableBody = $(ProductMap.attachments.attachmentsTableBody);
    this.prototypeTemplate = this.$attachmentsCollection.data('prototype');
    this.prototypeName = this.$attachmentsCollection.data('prototypeName');
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.router = new Router();
    this.init();
  }

  /**
   * @private
   */
  init() {
    this.onAttachmentSubmit();
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
      afterLoad: () => {
        const form = $('.fancybox-iframe').contents().find('form');
        const {fancybox} = parent.$;

        form.on('click', '.cancel-btn', () => {
          fancybox.close();
        });

        form.submit((e) => {
          this.eventEmitter.emit(ProductEventMap.attachments.newAttachmentSubmitted, {e, fancybox});
        });
      },
    });
  }

  /**
   * @private
   */
  onAttachmentSubmit() {
    this.eventEmitter.on(ProductEventMap.attachments.newAttachmentSubmitted, (eventData) => {
      const submitEvent = eventData.e;
      submitEvent.preventDefault();

      createAttachment(submitEvent.currentTarget).then((resp) => {
        if (resp.errors) {
          Object.values(resp.errors).forEach((error) => {
            $.growl.error({message: error});
          });

          return;
        }
        eventData.fancybox.close();

        getAttachmentInfo(resp.attachmentId).then((response) => {
          this.addAttachmentRow(response.attachmentInfo);
        });
      });
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
  addAttachmentRow(attachmentInfo) {
    const rowIndex = this.$attachmentsTableBody.find(ProductMap.attachments.attachedFileRow).length;
    const $row = $(this.getPrototypeRow(rowIndex));
    const attachmentId = attachmentInfo.id;
    const $attachmentIdInput = $(ProductMap.attachments.tableRow.attachmentIdInput(rowIndex), $row);
    const $attachmentNamePreview = $(ProductMap.attachments.tableRow.attachmentNamePreview, $row);
    const $attachmentFilenamePreview = $(ProductMap.attachments.tableRow.attachmentFilenamePreview, $row);
    const $attachmentTypePreview = $(ProductMap.attachments.tableRow.attachmentTypePreview, $row);

    $attachmentIdInput.val(attachmentId);
    $attachmentNamePreview.html(attachmentInfo.name);
    $attachmentFilenamePreview.html(attachmentInfo.filename);
    $attachmentTypePreview.html(attachmentInfo.mimeType);

    this.$attachmentsTableBody.append($row);
    this.eventEmitter.emit(ProductEventMap.attachments.rowAdded, {attachmentId});
    this.refreshState();
  }

  /**
   * @private
   */
  refreshState() {
    const isEmpty = this.$attachmentsTableBody.find(ProductMap.attachments.attachedFileRow).length === 0;
    const $emptyState = $(ProductMap.attachments.emptyState);

    if (isEmpty) {
      $emptyState.removeClass('d-none');
      this.$attachmentsCollection.addClass('d-none');
    } else {
      $emptyState.addClass('d-none');
      this.$attachmentsCollection.removeClass('d-none');
    }
  }

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
