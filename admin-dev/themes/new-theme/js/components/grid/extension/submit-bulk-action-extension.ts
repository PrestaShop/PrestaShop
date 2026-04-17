/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import ConfirmModal from '@components/modal';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Handles submit of grid actions
 */
export default class SubmitBulkActionExtension {
  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getContainer()
      .on('click', GridMap.bulks.submitAction, (event: JQueryEventObject) => {
        this.submit(event, grid);
      });
  }

  /**
   * Handle bulk action submitting
   *
   * @param {Event} event
   * @param {Grid} grid
   *
   * @private
   */
  private submit(event: JQueryEventObject, grid: Grid): void {
    const $submitBtn = $(event.currentTarget);
    const confirmMessage = $submitBtn.data('confirm-message');
    const confirmTitle = $submitBtn.data('confirmTitle');

    if (confirmMessage !== undefined && confirmMessage.length > 0) {
      if (confirmTitle !== undefined) {
        this.showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle);
      } else if (window.confirm(confirmMessage)) {
        this.postForm($submitBtn, grid);
      }
    } else {
      this.postForm($submitBtn, grid);
    }
  }

  /**
   * @param {jQuery} $submitBtn
   * @param {Grid} grid
   * @param {string} confirmMessage
   * @param {string} confirmTitle
   */
  private showConfirmModal(
    $submitBtn: JQuery<Element>,
    grid: Grid,
    confirmMessage: string,
    confirmTitle: string,
  ): void {
    const confirmButtonLabel = $submitBtn.data('confirmButtonLabel');
    const closeButtonLabel = $submitBtn.data('closeButtonLabel');
    const confirmButtonClass = $submitBtn.data('confirmButtonClass');

    const modal = new ConfirmModal(
      {
        id: GridMap.confirmModal(grid.getId()),
        confirmTitle,
        confirmMessage,
        confirmButtonLabel,
        closeButtonLabel,
        confirmButtonClass,
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
