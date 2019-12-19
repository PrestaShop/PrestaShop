/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

/**
 * Handles submit of grid actions
 */
export default class SubmitBulkActionExtension {
  constructor() {
    return {
      extend: (grid) => this.extend(grid),
    };
  }

  /**
   * Extend grid with bulk action submitting
   *
   * @param {Grid} grid
   */
  extend(grid) {
    grid.getContainer().on('click', '.js-bulk-action-submit-btn', (event) => {
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
  submit(event, grid) {
    const $submitBtn = $(event.currentTarget);
    const confirmMessage = $submitBtn.data('confirm-message');
    const confirmTitle = $submitBtn.data('confirmTitle');

    if (confirmMessage !== undefined && 0 < confirmMessage.length) {
      if (confirmTitle !== undefined) {
        this.showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle);
      } else if (confirm(confirmMessage)) {
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
  showConfirmModal($submitBtn, grid, confirmMessage, confirmTitle) {
    const confirmButtonLabel = $submitBtn.data('confirmButtonLabel');
    const confirmButtonClass = $submitBtn.data('confirmButtonClass');

    const $modal = $('#' + grid.getId() + '_grid_confirm_modal');
    $('.confirm-message', $modal).html(confirmMessage);
    $('.modal-title', $modal).html(confirmTitle);
    $('.modal-header', $modal).toggle(confirmTitle.length > 0);

    const $confirmButton = $('.btn-confirm-submit', $modal);
    $confirmButton.className = 'btn btn-confirm-submit btn-lg';
    $confirmButton.addClass(confirmButtonClass);
    $confirmButton.html(confirmButtonLabel);
    $confirmButton.off('click').on('click', () => this.postForm($submitBtn, grid));

    $modal.modal('show');
  }

  /**
   * @param {jQuery} $submitBtn
   * @param {Grid} grid
   */
  postForm($submitBtn, grid) {
    const $form = $('#' + grid.getId() + '_filter_form');

    $form.attr('action', $submitBtn.data('form-url'));
    $form.attr('method', $submitBtn.data('form-method'));
    $form.submit();
  }
}
