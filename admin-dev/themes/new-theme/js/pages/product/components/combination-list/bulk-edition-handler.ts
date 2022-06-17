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

import {FormIframeModal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ProductEvents from '@pages/product/product-event-map';
import CombinationsService from '@pages/product/services/combinations-service';
import {EventEmitter} from 'events';
import BulkChoicesSelector from '@pages/product/components/combination-list/bulk-choices-selector';
import ProgressModal from '@components/modal/progress-modal';
import ImageSelector from '@pages/product/combination/form/image-selector';
import QuantityModeSwitcher from '@pages/product/combination/QuantityModeSwitcher';
import {createElement} from "dropzone";

const CombinationMap = ProductMap.combinations;
const CombinationEvents = ProductEvents.combinations;

/**
 * This components handles the bulk edition of the combination list.
 */
export default class BulkEditionHandler {
  readonly productId: number;

  private combinationsService: CombinationsService;

  private eventEmitter: EventEmitter;

  private tabContainer: HTMLDivElement;

  private bulkChoicesSelector: BulkChoicesSelector;

  private formModal: FormIframeModal | null

  constructor(
    productId: number,
    eventEmitter: EventEmitter,
    bulkChoicesSelector: BulkChoicesSelector,
    combinationsService: CombinationsService,
  ) {
    this.formModal = null;
    this.productId = productId;
    this.eventEmitter = eventEmitter;
    this.bulkChoicesSelector = bulkChoicesSelector;
    this.combinationsService = combinationsService;
    this.tabContainer = document.querySelector<HTMLDivElement>(CombinationMap.combinationManager)!;

    this.init();
  }

  private init(): void {
    this.getBulkEditFormBtn()!.addEventListener('click', () => this.showFormModal());
  }

  private getBulkEditFormBtn(): HTMLButtonElement | null {
    const bulkEditionFormBtn = this.tabContainer.querySelector<HTMLButtonElement>(CombinationMap.bulkCombinationFormBtn);

    if (!(bulkEditionFormBtn instanceof HTMLButtonElement)) {
      console.error(`${CombinationMap.bulkCombinationFormBtn} was expected to be HTMLButtonElement`);
      return null;
    }

    return bulkEditionFormBtn;
  }

  /**
   * @param formContent can be used to replace original form content that was loaded from url with a new one
   * (e.g. the one returned from controller with already rendered form errors)
   */
  private async showFormModal(
    formContent?: HTMLFormElement|null,
  ): Promise<void> {
    const bulkEditionFormBtn = this.getBulkEditFormBtn()!;
    const {modalConfirmLabel, modalCancelLabel} = bulkEditionFormBtn.dataset;
    const {formUrl} = bulkEditionFormBtn.dataset;

    if (typeof formUrl !== 'string') {
      console.error('Mandatory attribute "data-form-url" is missing');
      return;
    }
    const selectedCombinationIds = await this.bulkChoicesSelector.getSelectedIds();
    const selectedCombinationsCount = selectedCombinationIds.length;
    let initialSerializedData: string;

    const iframeModal: FormIframeModal = new FormIframeModal({
      id: CombinationMap.bulkFormModalId,
      modalTitle: bulkEditionFormBtn.innerHTML,
      formUrl,
      autoSizeContainer: 'form[name="bulk_combination"]',
      closable: true,
      confirmButtonLabel: modalConfirmLabel?.replace(/%combinations_number%/, String(selectedCombinationsCount)),
      closeButtonLabel: modalCancelLabel,
      onFormLoaded: (form: HTMLFormElement) => {
        this.eventEmitter.emit('bulkFormLoaded', form);
        // Disable submit button as long as the form data has not changed
        iframeModal.modal.confirmButton?.setAttribute('disabled', 'disabled');
        // if (formContent) {
        //   form.replaceWith(formContent);
        //
        //   // eslint-disable-next-line no-param-reassign
        //   form = iframeModal.modal.iframe.contentWindow?.document
        //     .querySelector<HTMLFormElement>('form[name="bulk_combination"]') as HTMLFormElement;
        // }

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
      formConfirmCallback: (form: HTMLFormElement) => this.submitForm(form),
    });

    this.formModal = iframeModal;
    iframeModal.show();
  }

  private serializeForm(form: HTMLFormElement): string {
    // @ts-ignore
    return new URLSearchParams(new FormData(form)).toString();
  }

  private async submitForm(form: HTMLFormElement): Promise<void> {
    const combinationIds = await this.bulkChoicesSelector.getSelectedIds();
    const bulkChunkSize = Number(form.dataset.bulkChunkSize);
    const abortController = new AbortController();

    const progressModal = new ProgressModal({
      id: CombinationMap.bulkProgressModalId,
      abortCallback: () => {
        stopProcess = true;
        abortController.abort();
      },
      progressionTitle: form.dataset.progressTitle,
      progressionMessage: form.dataset.progressMessage,
      closeLabel: form.dataset.closeLabel,
      abortProcessingLabel: form.dataset.stopProcessing,
      errorsMessage: form.dataset.errorsMessage,
      backToProcessingLabel: form.dataset.backToProcessing,
      downloadErrorLogLabel: form.dataset.downloadErrorLog,
      viewErrorLogLabel: form.dataset.viewErrorLog,
      viewErrorTitle: form.dataset.viewErrorTitle,
      total: combinationIds.length,
    });
    progressModal.show();
    let stopProcess = false;
    let doneCount = 0;
    while (combinationIds.length) {
      const chunkIds: number[] = combinationIds.splice(0, bulkChunkSize);

      if (stopProcess) {
        break;
      }

      let data: Record<string, any>;

      try {
        // eslint-disable-next-line no-await-in-loop
        const response: Response = await this.combinationsService.bulkUpdate(
          this.productId,
          chunkIds,
          new FormData(form),
        );
          // eslint-disable-next-line no-await-in-loop
        data = await response.json();
        if (data.formContent) {
          progressModal.hide();
          if (this.formModal) {
            this.formModal.show();
            this.eventEmitter.on('bulkFormLoaded', (modalForm: HTMLFormElement) => {
              const div = document.createElement('div');
              div.innerHTML = data.formContent.trim();

              // @ts-ignore
              modalForm.replaceWith(div.firstChild);
              // const currentForm = this.formModal.modal.iframe.querySelector(CombinationMap.editionForm);
              // currentForm?.replaceWith(data.formContent);
            });
          }

          return;
        }
      } catch (e) {
        data = {
          errors: `Something went wrong with IDs ${chunkIds.join(', ')}: ${e.message ?? ''}`,
        };
      }

      doneCount += chunkIds.length;
      progressModal.updateProgress(doneCount);

      if (!data.success) {
        if (data.errors && Array.isArray(data.errors)) {
          data.errors.forEach((error: string) => {
            progressModal.addError(error);
          });
        } else {
          progressModal.addError(data.errors ?? data.error ?? data.message);
        }
      }
    }
    progressModal.completeProgress();
    this.eventEmitter.emit(CombinationEvents.bulkUpdateFinished);
  }
}
