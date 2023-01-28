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
import {Grid} from '@js/types/grid';
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
