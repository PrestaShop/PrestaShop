/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import TableSorting from '@app/utils/table-sorting';
import GridMap from '@components/grid/grid-map';

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
export default class SortingExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    const $sortableTable = grid.getContainer().find(GridMap.table);

    new TableSorting($sortableTable).attach();
  }
}
