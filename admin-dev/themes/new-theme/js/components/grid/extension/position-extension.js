/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import tableDnD from "tablednd/dist/jquery.tablednd.min";

const $ = window.$;

/**
 * Class FiltersResetExtension extends grid with filters resetting
 */
export default class PositionExtension {

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    this._addIdsToGridTableRows(grid);

    grid.getContainer().find('.js-grid-table').tableDnD({
      dragHandle: '.js-drag-handle',
      onDrop: function(table, row) {
        console.log(table, row);
      },
    });
  }

  /**
   * Add ID's to Grid table rows to make tableDnD.onDrop() function work.
   *
   * @param {Grid} grid
   *
   * @private
   */
  _addIdsToGridTableRows(grid) {
    grid.getContainer().find('.js-grid-table > tbody > tr').each((index, tableRow) => {
      if (typeof $(tableRow).attr('id') === 'undefined') {
        $(tableRow).attr('id', 'row-' + index);
      }
    });
  }
}
