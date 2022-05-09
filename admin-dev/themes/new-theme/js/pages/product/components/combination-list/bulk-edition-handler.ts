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

import {ConfirmModal, FormIframeModal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ProductEvents from '@pages/product/product-event-map';
import CombinationsService from '@pages/product/services/combinations-service';
import {EventEmitter} from 'events';
import BulkChoicesSelector from '@pages/product/components/combination-list/bulk-choices-selector';
import {notifyFormErrors} from '@components/form/helpers';

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

  constructor(
    productId: number,
    eventEmitter: EventEmitter,
    bulkChoicesSelector: BulkChoicesSelector,
    combinationsService: CombinationsService,
  ) {
    this.productId = productId;
    this.eventEmitter = eventEmitter;
    this.bulkChoicesSelector = bulkChoicesSelector;
    this.combinationsService = combinationsService;
    this.tabContainer = document.querySelector<HTMLDivElement>(CombinationMap.externalCombinationTab)!;

    this.init();
  }

  private init(): void {
    const bulkEditionFormBtn = this.tabContainer.querySelector<HTMLButtonElement>(CombinationMap.bulkCombinationFormBtn);

    if (!(bulkEditionFormBtn instanceof HTMLButtonElement)) {
      console.error(`${CombinationMap.bulkCombinationFormBtn} was expected to be HTMLButtonElement`);
      return;
    }
    const {modalConfirmLabel, modalCancelLabel} = bulkEditionFormBtn.dataset;
    const {formUrl} = bulkEditionFormBtn.dataset;

    if (typeof formUrl !== 'string') {
      console.error('Mandatory attribute "data-form-url" is missing');
      return;
    }

    bulkEditionFormBtn.addEventListener('click', () => this.showFormModal(
      formUrl,
      bulkEditionFormBtn.innerHTML,
      modalConfirmLabel || 'Confirm',
      modalCancelLabel || 'Cancel',
    ));
  }

  private async showFormModal(
    formUrl: string,
    modalTitle: string,
    confirmButtonLabel: string,
    closeButtonLabel: string,
  ): Promise<void> {
    const selectedCombinationIds = await this.bulkChoicesSelector.getSelectedIds();
    const selectedCombinationsCount = selectedCombinationIds.length;
    let initialSerializedData: string;
    const iframeModal = new FormIframeModal({
      id: CombinationMap.bulkFormModalId,
      modalTitle,
      formUrl,
      autoSizeContainer: 'form[name="bulk_combination"]',
      closable: true,
      confirmButtonLabel: confirmButtonLabel.replace(/%combinations_number%/, String(selectedCombinationsCount)),
      closeButtonLabel,
      onFormLoaded: (form: HTMLFormElement) => {
        // Disable submit button as long as the form data has not changed
        iframeModal.modal.confirmButton?.setAttribute('disabled', 'disabled');

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
      formConfirmCallback: (form: HTMLFormElement) => {
        this.submitForm(form);
      },
    });

    iframeModal.show();
  }

  private serializeForm(form: HTMLFormElement): string {
    // @ts-ignore
    return new URLSearchParams(new FormData(form)).toString();
  }

  private async submitForm(form: HTMLFormElement): Promise<void> {
    const progressModal = this.showProgressModal();
    const selectedIds = await this.bulkChoicesSelector.getSelectedIds();
    const progressModalElement = document.getElementById(CombinationMap.bulkProgressModalId);

    let progress = 1;

    for (let i = 0; i < selectedIds.length; i += 1) {
      const combinationId = selectedIds[i];

      // @todo when the ProgressModal will be integrated this will update it after each request
      try {
        // eslint-disable-next-line no-await-in-loop
        const response: Response = await this.combinationsService.bulkUpdate(
          this.productId,
          combinationId,
          new FormData(form),
        );
        // eslint-disable-next-line no-await-in-loop
        const jsonResponse = await response.json();

        if (jsonResponse.errors) {
          notifyFormErrors(jsonResponse);
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
