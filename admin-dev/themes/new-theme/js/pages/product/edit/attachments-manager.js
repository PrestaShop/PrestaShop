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
import {getAttachmentInfo} from '@pages/product/services/attachments-service';
import {FormIframeModal} from '@components/modal/form-iframe-modal';
import EntitySearchInput from '@components/entity-search-input';

const {$} = window;

export default class AttachmentsManager {
  constructor() {
    this.$attachmentsContainer = $(ProductMap.attachments.attachmentsContainer);
    this.$searchAttributeInput = $(ProductMap.attachments.searchAttributeInput);
    this.$addAttachmentBtn = $(ProductMap.attachments.addAttachmentBtn, this.$attachmentsContainer);
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.router = new Router();
    this.init();
  }

  /**
   * @private
   */
  init() {
    this.initAddAttachmentIframe();
    this.initSearchInput();
  }

  /**
   * @private
   */
  initAddAttachmentIframe() {
    this.$addAttachmentBtn.on('click', (event) => {
      event.preventDefault();

      const iframeModal = new FormIframeModal({
        id: 'modal-create-product-attachment',
        modalTitle: this.$addAttachmentBtn.data('modalTitle'),
        formSelector: 'form[name="attachment"]',
        formUrl: $(event.target).prop('href'),
        closable: true,
        onFormLoaded: (form, formData, dataAttributes) => {
          if (dataAttributes && dataAttributes.attachmentId) {
            const successMessage = this.$addAttachmentBtn.data('successCreateMessage');
            iframeModal.displayMessage(`<div class="alert alert-success d-print-none m-2" role="alert">
              <div class="alert-text">
                <p>${successMessage}</p>
              </div>
            </div>`);

            getAttachmentInfo(dataAttributes.attachmentId).then((response) => {
              this.entitySearchInput.addItem(response.attachmentInfo);
              setTimeout(() => { iframeModal.hide(); }, 2000);
            });
          }
        },
      });
      iframeModal.show();
    });
  }

  initSearchInput() {
    this.entitySearchInput = new EntitySearchInput(this.$searchAttributeInput, {
      onRemovedContent: () => {
        this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
      },
      onSelectedContent: () => {
        this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
      },
    });
  }
}
