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
import GridMap from '@components/grid/grid-map';
import Router from '@components/router';

const {$} = window;

/**
 * Handles submit of grid actions
 */
export default class AjaxBulkActionExtension {
  stopProcess = false;
  router = new Router();
  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getContainer()
      .on('click', GridMap.bulks.ajaxAction, (event: JQueryEventObject) => {
        let checkboxes = $('.js-bulk-action-checkbox:checked');

        const ajaxButton = $(event.currentTarget);
        this.submitForm(ajaxButton, checkboxes);
    });
  }

  private async submitForm(ajaxButton: JQuery<Element>, checkboxes: JQuery<Element>)
  {
    let total = checkboxes.length;
    let stopProcess = false;
    const modalTitle = ajaxButton.data('modalTitle');
    const modalClose = ajaxButton.data('modalClose');
    const modalStopProcessing = ajaxButton.data('modalStopProcessing');
    const modalErrorsOccurred = ajaxButton.data('modalErrorsOccurred');
    const modalBackToProcessing = ajaxButton.data('modalBackToProcessing');
    const modalDownloadErrorLog = ajaxButton.data('modalDownloadErrorLog');
    const modalViewErrorLog = ajaxButton.data('modalViewErrorLog');
    const modalViewErrorLogTitle = ajaxButton.data('modalViewErrorLogTitle');
    const modal = new ProgressModal(
      {
        cancelCallback: () => {stopProcess = true;},
        modalTitle,
        modalClose,
        modalStopProcessing,
        modalErrorsOccurred,
        modalBackToProcessing,
        modalDownloadErrorLog,
        modalViewErrorLog,
        modalViewErrorLogTitle,
        total
      }
    );

    modal.show();
    let doneCount = 0;

    for (let i = 0; i < checkboxes.length; i += 1) {
      const checkbox = checkboxes[i];
      if (stopProcess) {
        return false;
      }
      // eslint-disable-next-line no-await-in-loop
      const data = await this.callAjaxAction(ajaxButton, checkbox, modal, doneCount);
      doneCount++;
      if (data.success) {
        modal.updateCount(doneCount);
      } else {
        modal.updateCount(doneCount);
        modal.addError(data.message);
      }
    }
    modal.finishProcess();
  }

  private callAjaxAction($ajaxButton: JQuery<Element>, checkbox: Element, modal: ProgressModal, doneCount: number): JQuery.jqXHR
  {
    return $.ajax({
      url: this.router.generate($ajaxButton.data('ajax-url')),
      type: 'POST',
      data: { id: checkbox.getAttribute('value') },
        success(data) {
          return data;
        }
    });
  }
}
