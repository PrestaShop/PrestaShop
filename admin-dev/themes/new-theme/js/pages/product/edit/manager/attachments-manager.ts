/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import Router from '@components/router';
import {getAttachmentInfo} from '@pages/product/service/attachment';
import {FormIframeModal} from '@components/modal';
import EntitySearchInput from '@components/entity-search-input';
import {EventEmitter} from 'events';

const {$} = window;

export default class AttachmentsManager {
  private $attachmentsContainer: JQuery;

  private $searchAttributeInput: JQuery;

  private $addAttachmentBtn: JQuery;

  private eventEmitter: EventEmitter;

  private router: Router;

  private entitySearchInput!: EntitySearchInput;

  constructor() {
    this.$attachmentsContainer = $(ProductMap.attachments.attachmentsContainer);
    this.$searchAttributeInput = $(ProductMap.attachments.searchAttributeInput);
    this.$addAttachmentBtn = $(ProductMap.attachments.addAttachmentBtn, this.$attachmentsContainer);
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.router = new Router();
    this.init();
  }

  private init(): void {
    this.initAddAttachmentIframe();
    this.initSearchInput();
  }

  private initAddAttachmentIframe(): void {
    this.$addAttachmentBtn.on('click', (event) => {
      event.preventDefault();

      const iframeModal = new FormIframeModal({
        id: 'modal-create-product-attachment',
        modalTitle: this.$addAttachmentBtn.data('modalTitle'),
        formSelector: 'form[name="attachment"]',
        formUrl: $(event.currentTarget).prop('href'),
        closable: true,
        onFormLoaded: (form: HTMLElement, formData: FormData, dataAttributes: DOMStringMap | null): void => {
          if (dataAttributes && dataAttributes.attachmentId) {
            const successMessage = this.$addAttachmentBtn.data('successCreateMessage');
            $.growl({
              message: successMessage,
              duration: 4000,
            });
            iframeModal.showLoading();
            iframeModal.hide();

            getAttachmentInfo(Number(dataAttributes.attachmentId)).then((response) => {
              this.entitySearchInput.addItem(response.attachmentInfo);
            });
          }
        },
      });
      iframeModal.show();
    });
  }

  private initSearchInput(): void {
    this.entitySearchInput = new EntitySearchInput(this.$searchAttributeInput, {
      onRemovedContent: () => {
        this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
      },
      onSelectedContent: () => {
        this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
      },
      suggestionTemplate: (entity: any): string => `
        <div class="search-suggestion">${entity.attachment_id} - ${entity.name}</div>
      `,
    });
  }
}
