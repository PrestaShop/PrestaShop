/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

/**
 * Class ReloadListExtension extends grid with "List reload" action
 */
export default class ReloadListExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getHeaderContainer()
      .on('click', GridMap.commonRefreshListAction, () => {
        window.location.reload();
      });
  }
}
