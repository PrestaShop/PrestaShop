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

import {Grid} from '@PSTypes/grid';
import ProgressModal from '@components/modal/progress-modal';
import ConfirmModal from '@components/modal/confirm-modal';
import GridMap from '@components/grid/grid-map';
import Router from '@components/router';

import ClickEvent = JQuery.ClickEvent;

const {$} = window;

/**
 * Handles submit of grid actions
 */
export default class AjaxBulkActionExtension {
  private router = new Router();

  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getContainer()
      .on('click', GridMap.bulks.ajaxAction, (event: ClickEvent) => {
        const $ajaxButton: JQuery<HTMLInputElement> = $<HTMLInputElement>(event.currentTarget);
        const $checkboxes: JQuery<HTMLInputElement> = $<HTMLInputElement>(GridMap.bulks.checkedCheckbox);
        const selectedIds: string[] = $checkboxes.get().map((checkbox: HTMLInputElement) => checkbox.value);

        if (selectedIds.length === 0) {
          return;
        }

        const confirmBulkAction = $ajaxButton.data('confirmBulkAction') ?? true;

        if (confirmBulkAction) {
          const progressionTitle = $ajaxButton.data('progressTitle');
          const closeButtonLabel = $ajaxButton.data('cancelLabel') || 'Cancel';
          const confirmTitle = $ajaxButton.data('confirmTitle') || 'Apply modifications';
          const bulkAction = $ajaxButton.data('bulkAction') ?? 'bulk-action';

          const confirmModal = new ConfirmModal({
            id: GridMap.actions.ajaxBulkActionConfirmModal(grid.id, bulkAction),
            modalTitle: confirmTitle,
            closeButtonLabel,
            confirmMessage: progressionTitle.replace('%total%', selectedIds.length),
            confirmButtonLabel: confirmTitle,
            confirmCallback: () => {
              this.submitForm(grid, $<HTMLInputElement>(event.currentTarget), selectedIds);
            },
          });
          confirmModal.show();
        } else {
          this.submitForm(grid, $<HTMLInputElement>(event.currentTarget), selectedIds);
        }
      });
  }

  private async submitForm(grid: Grid, $ajaxButton: JQuery<HTMLInputElement>, selectedIds: string[]): Promise<void> {
    const bulkChunkSize = $ajaxButton.data('bulkChunkSize') ?? 10;
    const reloadAfterBulk = $ajaxButton.data('reloadAfterBulk') ?? true;

    const bulkAction = $ajaxButton.data('bulkAction') ?? 'bulk-action';
    const progressionTitle = $ajaxButton.data('progressTitle');
    const progressionMessage = $ajaxButton.data('progressMessage');
    const closeLabel = $ajaxButton.data('closeLabel');
    const abortProcessingLabel = $ajaxButton.data('stopProcessing');
    const errorsMessage = $ajaxButton.data('errorsMessage');
    const backToProcessingLabel = $ajaxButton.data('backToProcessing');
    const downloadErrorLogLabel = $ajaxButton.data('downloadErrorLog');
    const viewErrorLogLabel = $ajaxButton.data('viewErrorLog');
    const viewErrorTitle = $ajaxButton.data('viewErrorTitle');

    const abortController = new AbortController();

    const modal = new ProgressModal({
      id: GridMap.actions.ajaxBulkActionProgressModal(grid.id, bulkAction),
      abortCallback: () => {
        stopProcess = true;
        abortController.abort();
      },
      closeCallback: () => {
        if (reloadAfterBulk) {
          window.location.reload();
        }
      },
      progressionTitle,
      progressionMessage,
      closeLabel,
      abortProcessingLabel,
      errorsMessage,
      backToProcessingLabel,
      downloadErrorLogLabel,
      viewErrorLogLabel,
      viewErrorTitle,
      total: selectedIds.length,
    });
    modal.show();

    let stopProcess = false;
    let doneCount = 0;
    while (selectedIds.length) {
      const chunkIds: string[] = selectedIds.splice(0, bulkChunkSize);

      if (stopProcess) {
        break;
      }

      let data: Record<string, any>;
      try {
        // eslint-disable-next-line no-await-in-loop
        const response = await this.callAjaxAction($ajaxButton, chunkIds, abortController.signal);
        // eslint-disable-next-line no-await-in-loop
        data = await response.json();
      } catch (e) {
        data = {error: `Something went wrong with IDs ${chunkIds.join(', ')}: ${e.message ?? ''}`};
      }

      doneCount += chunkIds.length;
      modal.updateProgress(doneCount);

      if (!data.success) {
        if (data.errors && Array.isArray(data.errors)) {
          data.errors.forEach((error:string) => {
            modal.addError(error);
          });
        } else {
          modal.addError(data.errors ?? data.error ?? data.message);
        }
      }
    }

    modal.completeProgress();
  }

  private callAjaxAction($ajaxButton: JQuery<HTMLInputElement>, chunkIds: string[], abortSignal: AbortSignal): Promise<Response> {
    const requestParamName: string = $ajaxButton.data('requestParamName') ?? 'bulk_ids';
    const routeParams: Record<string, any> = $ajaxButton.data('routeParams') ?? {};
    const routeMethod: string = $ajaxButton.data('routeMethod') ?? 'POST';

    const formData: FormData = new FormData();
    chunkIds.forEach((chunkId: string, index: number) => {
      formData.append(`${requestParamName}[${index}]`, chunkId);
    });

    let requestMethod: string;

    // For PATCH and DELETE request we use a POST request but we use the _method for Symfony to handle it
    switch (routeMethod.toUpperCase()) {
      case 'PATCH':
      case 'DELETE':
        requestMethod = 'POST';
        break;
      default:
        requestMethod = routeMethod;
        break;
    }

    return fetch(this.router.generate($ajaxButton.data('ajax-url'), routeParams), {
      method: requestMethod,
      body: formData,
      headers: {
        _method: routeMethod,
      },
      signal: abortSignal,
    });
  }
}
