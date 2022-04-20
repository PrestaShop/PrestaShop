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
import ProgressModal, {ProgressModalContainer} from '@components/modal/progress-modal';
import GridMap from '@components/grid/grid-map';

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
    let AjaxBulkExtension = this;

    grid
      .getContainer()
      .on('click', GridMap.bulks.ajaxAction, (event: JQueryEventObject) => {
        let checkboxes = $('.js-bulk-action-checkbox:checked');
        let total = checkboxes.length;

        const $ajaxButton = $(event.currentTarget);

        const modal = this.showProgressModal(
          $ajaxButton,
          grid,
          total,
        );
        let doneCount = 0;


        for (let i = 0; i < checkboxes.length; i += 1) {
          const checkbox = checkboxes[i];
          if (AjaxBulkExtension.stopProcess) {
            return false;
          }
          $.ajax({
            type: "POST",
            headers: { "cache-control": "no-cache" },
            url: $ajaxButton.data('ajax-url'),
            data: { id: checkbox.getAttribute('value') },
            success(data) {
              doneCount++;
              if (data.success) {
                modal.modalActionSuccess(doneCount);
              } else {
                modal.modalActionError(doneCount, data.message);
              }
            }
          });
      }
    });
  }

  private cancelAjaxAction(): void
  {
    this.stopProcess = true;
  }

  /**
   * @param {jQuery} $submitBtn
   * @param {Grid} grid
   * @param total
   */
  private showProgressModal(
    $submitBtn: JQuery<Element>,
    grid: Grid,
    total: number,
  ): ProgressModal {
    const modalTitle = $submitBtn.data('modalTitle');

    const modal = new ProgressModal(
      {
        modalTitle,
      },
      total,
      () => this.postForm($submitBtn, grid),
      () => this.cancelAjaxAction(),
    );

    modal.show();

    return modal;
  }

  /**
   * @param {jQuery} $submitBtn
   * @param {Grid} grid
   */
  private postForm($submitBtn: JQuery<Element>, grid: Grid): void {
    const $form = $(GridMap.filterForm(grid.getId()));

    $form.attr('action', $submitBtn.data('form-url'));
    $form.attr('method', $submitBtn.data('form-method'));
    $form.submit();
  }
}
