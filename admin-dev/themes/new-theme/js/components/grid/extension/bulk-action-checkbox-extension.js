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

const {$} = window;

/**
 * Class BulkActionSelectCheckboxExtension
 */
export default class BulkActionCheckboxExtension {
  /**
   * Extend grid with bulk action checkboxes handling functionality
   *
   * @param {Grid} grid
   */
  extend(grid) {
    this.handleBulkActionCheckboxSelect(grid);
    this.handleBulkActionSelectAllCheckbox(grid);
    this.handleBulkActionMultipleInputs(grid);
  }

  /**
   * @todo I am not sure if its the best way to do this. Basically the issue is that right now
   * checkbox gets checked due to native label -> input interaction(clicking on label checks input)
   * If I create multiple inputs they won't activate. One idea I had is to try to create multilple labels,
   * but that needs to position them in one place and make sure they are all clicked, probably a worse solution.
   *
   * In this case I prevent original action from happening, and instead toggle inputs via javascript.
   * I am not 100% this will not lead to some issues so needs to be carefully tested.
   *
   * Other alternative I can see for multiple inputs is to add them as datas, but it would need to change
   * whole form submiting process, so this is not an idea I am fond of.
   *
   * Handles input selection when there is more then one input per checkbox
   *
   * @param {Grid} grid
   *
   * @private
   */
  handleBulkActionMultipleInputs(grid) {
    grid.getContainer().on('click', '.md-checkbox-control', (e) => {
      e.preventDefault();
      const $checkbox = $(e.currentTarget).closest('.md-checkbox');
      const isChecked = $checkbox.find('input').is(':checked');
      $checkbox.find('input').prop('checked', !isChecked);
    });
  }


  /**
   * Handles "Select all" button in the grid
   *
   * @param {Grid} grid
   *
   * @private
   */
  handleBulkActionSelectAllCheckbox(grid) {
    grid.getContainer().on('change', '.js-bulk-action-select-all', (e) => {
      const $checkbox = $(e.currentTarget);
      const isChecked = $checkbox.is(':checked');

      if (isChecked) {
        this.enableBulkActionsBtn(grid);
      } else {
        this.disableBulkActionsBtn(grid);
      }

      grid.getContainer().find('.js-bulk-action-checkbox').prop('checked', isChecked);
    });
  }

  /**
   * Handles each bulk action checkbox select in the grid
   *
   * @param {Grid} grid
   *
   * @private
   */
  handleBulkActionCheckboxSelect(grid) {
    grid.getContainer().on('change', '.js-bulk-action-checkbox', () => {
      const checkedRowsCount = grid.getContainer().find('.js-bulk-action-checkbox:checked').length;

      if (checkedRowsCount > 0) {
        this.enableBulkActionsBtn(grid);
      } else {
        this.disableBulkActionsBtn(grid);
      }
    });
  }

  /**
   * Enable bulk actions button
   *
   * @param {Grid} grid
   *
   * @private
   */
  enableBulkActionsBtn(grid) {
    grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', false);
  }

  /**
   * Disable bulk actions button
   *
   * @param {Grid} grid
   *
   * @private
   */
  disableBulkActionsBtn(grid) {
    grid.getContainer().find('.js-bulk-actions-btn').prop('disabled', true);
  }
}
