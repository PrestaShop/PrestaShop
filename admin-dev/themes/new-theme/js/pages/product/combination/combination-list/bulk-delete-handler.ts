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
import {bulkDeleteCombinations} from '@pages/product/service/combination';
import BulkChoicesSelector from '@pages/product/combination/combination-list/bulk-choices-selector';
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

  private readonly bulkChoicesSelector: BulkChoicesSelector;

  constructor(
    productId: number,
    eventEmitter: EventEmitter,
    bulkChoicesSelector: BulkChoicesSelector,
  ) {
    this.productId = productId;
    this.eventEmitter = eventEmitter;
    this.bulkChoicesSelector = bulkChoicesSelector;

    this.init();
  }

  private async init(): Promise<void> {
    const bulkDeleteButtons = document.querySelectorAll<HTMLButtonElement>(CombinationMap.bulkDeleteBtn);

    bulkDeleteButtons.forEach((bulkDeleteBtn: HTMLButtonElement) => {
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
              await this.bulkDelete(selectedCombinationIds, bulkDeleteBtn);
            },
          );
          modal.show();
        } catch (error: any) {
          const errorMessage = error.response?.JSON ?? error;
          $.growl.error({message: errorMessage});
        }
      });
    });
  }

  private async bulkDelete(combinationIds: number[], bulkDeleteBtn: HTMLButtonElement): Promise<void> {
    const bulkChunkSize = Number(bulkDeleteBtn.dataset.bulkChunkSize);
    const abortController = new AbortController();

    const progressModal = new ProgressModal({
      id: CombinationMap.bulkProgressModalId,
      abortCallback: () => {
        stopProcess = true;
        abortController.abort();
      },
      closeCallback: () => this.eventEmitter.emit(CombinationEvents.bulkDeleteFinished),
      progressionTitle: bulkDeleteBtn.dataset.progressTitle,
      progressionMessage: bulkDeleteBtn.dataset.progressMessage,
      closeLabel: bulkDeleteBtn.dataset.closeLabel,
      abortProcessingLabel: bulkDeleteBtn.dataset.stopProcessing,
      errorsMessage: bulkDeleteBtn.dataset.errorsMessage,
      backToProcessingLabel: bulkDeleteBtn.dataset.backToProcessing,
      downloadErrorLogLabel: bulkDeleteBtn.dataset.downloadErrorLog,
      viewErrorLogLabel: bulkDeleteBtn.dataset.viewErrorLog,
      viewErrorTitle: bulkDeleteBtn.dataset.viewErrorTitle,
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
      let shopId = null;

      if (bulkDeleteBtn.id !== CombinationMap.bulkDeleteBtnAllShopsId) {
        shopId = <number> <unknown> bulkDeleteBtn.dataset.shopId;
      }

      try {
        // eslint-disable-next-line no-await-in-loop
        const response: Response = await bulkDeleteCombinations(
          this.productId,
          chunkIds,
          shopId,
          abortController.signal,
        );

        // eslint-disable-next-line no-await-in-loop
        data = await response.json();
        if (data.error) {
          progressModal.interruptProgress();
          stopProcess = true;
        }
      } catch (e: any) {
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
