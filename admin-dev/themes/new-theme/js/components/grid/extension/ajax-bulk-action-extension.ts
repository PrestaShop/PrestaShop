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
        const modalTitle = ajaxButton.data('modalTitle');

        this.submitForm(ajaxButton, checkboxes, modalTitle);
    });
  }

  private async submitForm(ajaxButton: JQuery<Element>, checkboxes: JQuery<Element>, modalTitle: string)
  {
    let total = checkboxes.length;
    let stopProcess = false;

    const modal = new ProgressModal(
      {
        cancelCallback: () => {stopProcess = true; console.log(stopProcess)},
        modalTitle,
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
      console.log(stopProcess);
      doneCount++;
      if (data.success) {
        modal.updateCount(doneCount);
      } else {
        modal.updateCount(doneCount);
        modal.addError(data.message);
      }
    }
  }

  private callAjaxAction($ajaxButton: JQuery<Element>, checkbox: Element, modal: ProgressModal, doneCount: number): JQuery.jqXHR
  {
    const router = new Router();
    console.log(router.generate($ajaxButton.data('ajax-url')));
    return $.ajax({
      url: router.generate($ajaxButton.data('ajax-url')),
      type: 'POST',
      data: { id: checkbox.getAttribute('value') },
        success(data) {
          return data;
        }
    });
  }
}
