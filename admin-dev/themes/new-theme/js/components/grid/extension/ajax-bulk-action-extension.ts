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
import AjaxProgressModal from '@components/modal/ajax-progress-modal';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Handles submit of grid actions
 */
export default class AjaxBulkActionExtension {
  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getContainer()
      .on('click', GridMap.bulks.ajaxAction, (event: JQueryEventObject) => {

        let total = $('.js-bulk-action-checkbox:checked').length;

        const $ajaxButton = $(event.currentTarget);

        this.showAjaxProgressModal(
          $ajaxButton,
          grid,
        );
        let done = 0;
        $('.js-bulk-action-checkbox:checked').each( function (i) {
          $.ajax({
            type: "POST",
            headers: { "cache-control": "no-cache" },
            url: $ajaxButton.data('ajax-url'),
            data: { id: $(this).val() },
            success(data) {
              done++;
              var progressionDone = done * 100 / total;
              // var progressionNext = (done+1) * 100 / total;
              $('#ajax_progressbar_done').width((100-progressionDone)+'%');
              $('#ajax_progressbar_info').width(progressionDone+'%');
              $('.progress-success-count').html(done + '/' + total);

              //$('#ajax_progressbar_next').width((progressionNext-progressionDone)+'%');
            }
          });
        })
      });
  }

  /**
   * @param {jQuery} $submitBtn
   * @param {Grid} grid
   * @param {string} confirmMessage
   * @param {string} confirmTitle
   * @param modalTitle
   * @param modalProgressTitle
   */
  private showAjaxProgressModal(
    $submitBtn: JQuery<Element>,
    grid: Grid,
  ): void {
    const confirmButtonLabel = $submitBtn.data('confirmButtonLabel');
    const closeButtonLabel = $submitBtn.data('closeButtonLabel');
    const confirmButtonClass = $submitBtn.data('confirmButtonClass');
    const confirmMessage = $submitBtn.data('confirmMessage');
    const modalTitle = $submitBtn.data('modalTitle');
    const modalProgressTitle = $submitBtn.data('modalProgressTitle');
    const modal = new AjaxProgressModal(
      {
        id: GridMap.confirmModal(grid.getId()),
        confirmMessage,
        confirmButtonLabel,
        closeButtonLabel,
        confirmButtonClass,
        modalTitle,
        modalProgressTitle,
      },
      () => this.postForm($submitBtn, grid),
    );

    modal.show();
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
