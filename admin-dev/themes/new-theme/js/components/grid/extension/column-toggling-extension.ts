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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import {Grid} from '@js/types/grid';
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
