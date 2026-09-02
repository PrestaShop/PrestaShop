/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

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
  extend(grid: Grid): void {
    this.handleBulkActionCheckboxStatus(grid);
    this.handleBulkActionCheckboxSelect(grid);
    this.handleBulkActionSelectAllCheckbox(grid);
  }

  /**
   * Disable/Enable "Select all" button in the grid
   *
   * @param {Grid} grid
   *
   * @private
   */
  private handleBulkActionCheckboxStatus(grid: Grid) {
    const gridBulkActionSelectAll = grid.getContainer().find(GridMap.bulks.actionSelectAll);
    gridBulkActionSelectAll.prop(
      'disabled',
      grid.getContainer().find(GridMap.bulks.bulkActionCheckbox).length === 0,
    );
  }

  /**
   * Handles "Select all" button in the grid
   *
   * @param {Grid} grid
   *
   * @private
   */
  private handleBulkActionSelectAllCheckbox(grid: Grid) {
    grid.getContainer().on('change', GridMap.bulks.actionSelectAll, (e) => {
      const $checkbox = $(e.currentTarget);

      const isChecked = $checkbox.is(':checked');

      if (isChecked) {
        this.enableBulkActionsBtn(grid);
      } else {
        this.disableBulkActionsBtn(grid);
      }

      grid
        .getContainer()
        .find(GridMap.bulks.bulkActionCheckbox)
        .prop('checked', isChecked);
    });
  }

  /**
   * Handles each bulk action checkbox select in the grid
   *
   * @param {Grid} grid
   *
   * @private
   */
  private handleBulkActionCheckboxSelect(grid: Grid) {
    grid.getContainer().on('change', GridMap.bulks.bulkActionCheckbox, () => {
      const checkedRowsCount = grid
        .getContainer()
        .find(GridMap.bulks.checkedCheckbox).length;

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
  private enableBulkActionsBtn(grid: Grid): void {
    grid
      .getContainer()
      .find(GridMap.bulks.bulkActionBtn)
      .prop('disabled', false);
  }

  /**
   * Disable bulk actions button
   *
   * @param {Grid} grid
   *
   * @private
   */
  private disableBulkActionsBtn(grid: Grid): void {
    grid
      .getContainer()
      .find(GridMap.bulks.bulkActionBtn)
      .prop('disabled', true);
  }
}
