/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import resetSearch from '@app/utils/reset_search';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Class FiltersResetExtension extends grid with filters resetting
 */
export default class FiltersResetExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid.getContainer().on('click', GridMap.resetSearch, (event) => {
      resetSearch(
        $(event.currentTarget).data('url'),
        $(event.currentTarget).data('redirect'),
      );
    });
  }
}
