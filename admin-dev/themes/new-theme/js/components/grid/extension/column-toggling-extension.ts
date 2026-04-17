/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Class ReloadListExtension extends grid with "Column toggling" feature
 */
export default class ColumnTogglingExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    const $table = grid.getContainer().find(GridMap.table);
    $table.find(GridMap.togglableRow).on('click', (e) => {
      e.preventDefault();
      this.toggleValue($(e.delegateTarget));
    });
  }

  /**
   * @param {jQuery} row
   * @private
   */
  private toggleValue(row: JQuery) {
    const toggleUrl = row.data('toggleUrl');

    this.submitAsForm(toggleUrl);
  }

  /**
   * Submits request url as form
   *
   * @param {string} toggleUrl
   * @private
   */
  private submitAsForm(toggleUrl: string) {
    const $form = $('<form>', {
      action: toggleUrl,
      method: 'POST',
    }).appendTo('body');

    $form.submit();
  }
}
