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

import {ConfirmModal, IframeModal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ProductEvents from '@pages/product/product-event-map';
import CombinationsService from '@pages/product/services/combinations-service';
import {EventEmitter} from 'events';
import BulkChoicesSelector from '@pages/product/combination/bulk-choices-selector';

const CombinationMap = ProductMap.combinations;
const CombinationEvents = ProductEvents.combinations;

/**
 * This components handles the bulk actions of the combination list.
 */
export default class BulkFormHandler {
  readonly productId: number;

  private combinationsService: CombinationsService;

  private eventEmitter: EventEmitter;

  private tabContainer: HTMLDivElement;

  private bulkChoicesSelector: BulkChoicesSelector;

  constructor(productId: number, bulkChoicesSelector: BulkChoicesSelector) {
    this.productId = productId;
    this.bulkChoicesSelector = bulkChoicesSelector;
    this.combinationsService = new CombinationsService();
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.tabContainer = document.querySelector<HTMLDivElement>(CombinationMap.externalCombinationTab)!;

    this.init();
  }

  private init(): void {
    //@todo; what does this mean? why there are 2 almost same looking selectors?
    const bulkFormBtn = document.querySelector<HTMLButtonElement>(CombinationMap.bulkCombinationFormBtn);
    const bulkCombinationsBtn = this.tabContainer.querySelector<HTMLButtonElement>(CombinationMap.bulkCombinationFormBtn);

    // @todo: This is hard-coded but when the other bulk actions will be added this needs to be more generic via
    //        the use of data attributes:
    //           data-bulk-url: is the url to call for each selected IDs, by default only the ID is necessary in the data
    //           data-form-url: indicates that a form must be opened, upon submit the data is used to be sent to the bulk-url
    if (bulkCombinationsBtn && bulkFormBtn) {
      const {modalConfirmLabel, modalCancelLabel} = bulkCombinationsBtn.dataset;
      const {formUrl} = bulkFormBtn.dataset;

      if (formUrl) {
        bulkFormBtn.addEventListener('click', () => this.showFormModal(
          formUrl,
          bulkCombinationsBtn.innerHTML,
          modalConfirmLabel || 'Confirm',
          modalCancelLabel || 'Cancel',
        ));
      }
    }
  }

  private showFormModal(formUrl: string, modalTitle: string, confirmButtonLabel: string, closeButtonLabel: string): void {
    const selectedCombinationsCount = this.bulkChoicesSelector.getSelectedCheckboxes().length;

    let initialSerializedData: string;
    const iframeModal = new IframeModal({
      id: CombinationMap.bulkFormModalId,
      modalTitle,
      iframeUrl: formUrl,
      autoSizeContainer: 'form[name="bulk_combination"]',
      closable: true,
      confirmButtonLabel: confirmButtonLabel.replace(/%combinations_number%/, String(selectedCombinationsCount)),
      closeButtonLabel,
      onLoaded: (iframe: HTMLIFrameElement) => {
        // Disable submit button as long as the form data has not changed
        iframeModal.modal.confirmButton?.setAttribute('disabled', 'disabled');
        const form: HTMLFormElement | null = this.getIframeForm(iframe);

        if (form) {
          initialSerializedData = this.serializeForm(form);
          form.addEventListener('change', () => {
            const currentSerializedData: string = this.serializeForm(form);

            if (currentSerializedData === initialSerializedData) {
              iframeModal.modal.confirmButton?.setAttribute('disabled', 'disabled');
            } else {
              iframeModal.modal.confirmButton?.removeAttribute('disabled');
            }
          });
        }
      },
      confirmCallback: () => {
        if (iframeModal.modal.iframe.contentWindow) {
          // eslint-disable-next-line max-len
          const form: HTMLFormElement | null = iframeModal.modal.iframe.contentWindow.document.querySelector<HTMLFormElement>('form[name="bulk_combination"]');

          if (form) {
            this.submitForm(form);
          }
        }
      },
    });

    iframeModal.show();
  }

  private serializeForm(form: HTMLFormElement): string {
    // @ts-ignore
    return new URLSearchParams(new FormData(form)).toString();
  }

  private getIframeForm(iframe: HTMLIFrameElement): HTMLFormElement | null {
    if (iframe.contentWindow) {
      return iframe.contentWindow.document.querySelector<HTMLFormElement>('form[name="bulk_combination"]');
    }

    return null;
  }

  private async submitForm(form: HTMLFormElement): Promise<void> {
    const progressModal = this.showProgressModal();

    const checkboxes = this.bulkChoicesSelector.getSelectedCheckboxes();
    const progressModalElement = document.getElementById(CombinationMap.bulkProgressModalId);

    let progress = 1;

    for (let i = 0; i < checkboxes.length; i += 1) {
      const checkbox = checkboxes[i];

      // @todo when the ProgressModal will be integrated this will update it after each request
      try {
        // eslint-disable-next-line no-await-in-loop
        const response: Response = await this.combinationsService.bulkUpdate(
          this.productId,
          Number(checkbox.value),
          new FormData(form),
        );
        // eslint-disable-next-line no-await-in-loop
        const jsonResponse = await response.json();

        if (jsonResponse.errors) {
          Object.keys(jsonResponse.errors).forEach((field: string) => {
            if (Object.prototype.hasOwnProperty.call(jsonResponse.errors, field)) {
              const fieldErrors: string[] = jsonResponse.errors[field];
              const errors: string = fieldErrors.join(' ');
              $.growl.error({message: `${field}: ${errors}`});
            }
          });
        }
      } catch (error) {
        console.log(error);
      }

      //@todo: also related with temporary progress modal. Needs to be fixed according to new progress modal once its merged in #26004.
      const progressContent = progressModalElement?.querySelector<HTMLParagraphElement>('.progress-increment');

      if (progressContent) {
        progressContent.innerHTML = String(progress);
      }
      progress += 1;
    }

    progressModal.hide();

    this.eventEmitter.emit(CombinationEvents.bulkUpdateFinished);
  }

  private showProgressModal(): ConfirmModal {
    //@todo: Replace with new progress modal when introduced in #26004.
    const modal = new ConfirmModal(
      {
        id: CombinationMap.bulkProgressModalId,
        confirmMessage: '<div>Updating combinations: <p class="progress-increment"></p></div>',
      },
      () => null,
    );

    modal.show();

    return modal;
  }
}
