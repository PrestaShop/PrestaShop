/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';
import Router from '../../router';

const {$} = window;

/**
 * Class BulkOpenTabsExtension
 */
export default class BulkOpenTabsExtension {
  router: Router;

  constructor() {
    this.router = new Router();
  }

  /**
   * Extend grid with bulk action open tabs
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getContainer()
      .on('click', GridMap.bulks.openTabsBtn, (event: JQueryEventObject) => {
        this.openTabs(event, grid);
      });
  }

  /**
   * Handle bulk action opening tabs
   *
   * @param {Event} event
   * @param {Grid} grid
   *
   * @private
   */
  openTabs(event: JQueryEventObject, grid: Grid): void {
    const $submitBtn = $(event.currentTarget);
    const route = $submitBtn.data('route');
    const routeParamName = $submitBtn.data('routeParamName');
    const tabsBlockedMessage = $submitBtn.data('tabsBlockedMessage');

    const $checkboxes = grid.getContainer().find(GridMap.bulks.checkedCheckbox);
    let allTabsOpened = true;
    $checkboxes.each((i, element) => {
      const $checkbox = $(element);
      const routeParams = {};
      // @ts-ignore
      routeParams[routeParamName] = $checkbox.val();

      const handle = window.open(this.router.generate(route, routeParams));

      if (handle) {
        handle.blur();
        window.focus();
      } else {
        allTabsOpened = false;
      }

      if (!allTabsOpened) {
        alert(tabsBlockedMessage);
      }
    });
  }
}
