/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

/**
 * Responsible for grid filters search and reset button availability when filter inputs changes.
 */
export default class FiltersSubmitButtonEnablerExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    const $filtersRow = grid.getContainer().find(GridMap.columnFilters);
    $filtersRow.find(GridMap.gridSearchButton).prop('disabled', true);

    $filtersRow.find(GridMap.inputAndSelect).on('input dp.change', () => {
      $filtersRow.find(GridMap.gridSearchButton).prop('disabled', false);
      $filtersRow.find(GridMap.gridResetButton).prop('hidden', false);
    });
  }
}
