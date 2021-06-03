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
