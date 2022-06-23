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

import {ConfirmModal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ProductEvents from '@pages/product/product-event-map';
import CombinationsService from '@pages/product/services/combinations-service';
import BulkChoicesSelector from '@pages/product/components/combination-list/bulk-choices-selector';
import {EventEmitter} from 'events';
import ProgressModal from '@components/modal/progress-modal';

const CombinationMap = ProductMap.combinations;
const CombinationEvents = ProductEvents.combinations;

/**
 * This components handles the bulk deletion of the combination list.
 */
export default class BulkDeleteHandler {
  private readonly productId: number;

  private readonly eventEmitter: EventEmitter;

  private readonly combinationsService: CombinationsService;

  private readonly bulkChoicesSelector: BulkChoicesSelector;

  constructor(
    productId: number,
    eventEmitter: EventEmitter,
    bulkChoicesSelector: BulkChoicesSelector,
    combinationsService: CombinationsService,
  ) {
    this.productId = productId;
    this.eventEmitter = eventEmitter;
    this.combinationsService = combinationsService;
    this.bulkChoicesSelector = bulkChoicesSelector;

    this.init();
  }

  private async init(): Promise<void> {
    const bulkDeleteBtn = document.querySelector<HTMLButtonElement>(CombinationMap.bulkDeleteBtn);

    if (!(bulkDeleteBtn instanceof HTMLButtonElement)) {
      console.error(`${CombinationMap.bulkDeleteBtn} must be a HTMLButtonElement`);

      return;
    }

    bulkDeleteBtn.addEventListener('click', async () => {
      const selectedCombinationIds = await this.bulkChoicesSelector.getSelectedIds();

      try {
        const selectedCombinationsCount = selectedCombinationIds.length;
        const confirmLabel = bulkDeleteBtn.dataset.modalConfirmLabel
          ?.replace(/%combinations_number%/, String(selectedCombinationsCount));

        const modal = new ConfirmModal(
          {
            id: 'modal-confirm-delete-combinations',
            confirmTitle: bulkDeleteBtn.innerHTML,
            confirmMessage: bulkDeleteBtn.dataset.modalMessage,
            confirmButtonLabel: confirmLabel,
            closeButtonLabel: bulkDeleteBtn.dataset.modalCancelLabel,
            closable: true,
          },
          async () => {
            await this.bulkDelete(selectedCombinationIds);
          },
        );
        modal.show();
      } catch (error) {
        const errorMessage = error.response?.JSON ?? error;
        $.growl.error({message: errorMessage});
      }
    });
  }

  private async bulkDelete(combinationIds: number[]): Promise<void> {
    const $bulkDeleteBtn = $(CombinationMap.bulkDeleteBtn);
    const bulkChunkSize = Number($bulkDeleteBtn.data('bulkChunkSize'));
    const abortController = new AbortController();

    const progressModal = new ProgressModal({
      id: CombinationMap.bulkProgressModalId,
      abortCallback: () => {
        stopProcess = true;
        abortController.abort();
      },
      closeCallback: () => this.eventEmitter.emit(CombinationEvents.bulkDeleteFinished),
      progressionTitle: $bulkDeleteBtn.data('progressTitle'),
      progressionMessage: $bulkDeleteBtn.data('progressMessage'),
      closeLabel: $bulkDeleteBtn.data('closeLabel'),
      abortProcessingLabel: $bulkDeleteBtn.data('stopProcessing'),
      errorsMessage: $bulkDeleteBtn.data('errorsMessage'),
      backToProcessingLabel: $bulkDeleteBtn.data('backToProcessing'),
      downloadErrorLogLabel: $bulkDeleteBtn.data('downloadErrorLog'),
      viewErrorLogLabel: $bulkDeleteBtn.data('viewErrorLog'),
      viewErrorTitle: $bulkDeleteBtn.data('viewErrorTitle'),
      total: combinationIds.length,
    });
    progressModal.show();
    let stopProcess = false;
    let doneCount = 0;
    while (combinationIds.length) {
      if (stopProcess) {
        break;
      }

      const chunkIds: number[] = combinationIds.splice(0, bulkChunkSize);
      let data: Record<string, any>;

      try {
        // eslint-disable-next-line no-await-in-loop
        const response: Response = await this.combinationsService.bulkDeleteCombinations(
          this.productId,
          chunkIds,
          abortController.signal,
        );

        // eslint-disable-next-line no-await-in-loop
        data = await response.json();
        if (data.error) {
          progressModal.interruptProgress();
          stopProcess = true;
        }
      } catch (e) {
        data = {
          error: `Something went wrong with IDs ${chunkIds.join(', ')}: ${e.message ?? ''}`,
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
  }
}
