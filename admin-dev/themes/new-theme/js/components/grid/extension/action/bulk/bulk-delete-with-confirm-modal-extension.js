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
 * BulkDeleteExtension extends grid and enable a "are you sure to delete the selection" modal
 * to pop when bulk delete action is used
 */
export default class BulkDeleteWithModalConfirmExtension {

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    grid.getContainer().on('click', '.js-common_bulk_delete-grid-action', () => this._onBulkDeleteClick(grid));
  }

  /**
   * Invoked when clicking on the "delete selected items" toolbar button
   *
   * @param {Grid} grid
   *
   * @private
   */
  _onBulkDeleteClick(grid) {
    const $deleteBtn = $('.js-common_bulk_delete-grid-action');
    const $bulkDeleteForm = $('#' + grid.getId() + '_filter_form');

    $bulkDeleteForm.attr('action', $deleteBtn.data('form-url'));
    $bulkDeleteForm.attr('method', $deleteBtn.data('form-method'));

    const $modal = $('#' + grid.getId() + '_grid_confirm_deletion_modal');
    $modal.modal('show');

    $modal.on('click', '.btn-bulk_delete-submit', () => $bulkDeleteForm.submit());
  }
}
