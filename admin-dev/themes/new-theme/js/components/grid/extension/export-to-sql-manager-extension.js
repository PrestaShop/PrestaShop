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

const $ = window.$;

/**
 * Class ExportToSqlManagerExtension extends grid with exporting query to SQL Manager
 */
export default class ExportToSqlManagerExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    grid.getContainer().on('click', '.js-common_show_query-grid-action', () => this._onShowSqlQueryClick(grid));
    grid.getContainer().on('click', '.js-common_export_sql_manager-grid-action', () => this._onExportSqlManagerClick(grid));
  }

  /**
   * Invoked when clicking on the "show sql query" toolbar button
   *
   * @param {Grid} grid
   *
   * @private
   */
  _onShowSqlQueryClick(grid) {
    const query = grid.getContainer().find('.js-grid-table').data('query');

    const $sqlManagerForm = $('#' + grid.getId() + '_grid_common_show_query_modal_form');
    $sqlManagerForm.find('textarea[name="sql"]').val(query);

    const $modal = $('#' + grid.getId() + '_grid_common_show_query_modal');
    $modal.modal('show');

    $modal.on('click', '.btn-sql-submit', () => $sqlManagerForm.submit());
  }

  /**
   * Invoked when clicking on the "export to the sql query" toolbar button
   *
   * @param {Grid} grid
   *
   * @private
   */
  _onExportSqlManagerClick(grid) {
    const query = grid.getContainer().find('.js-grid-table').data('query');

    const $sqlManagerForm = $('#' + grid.getId() + '_grid_common_show_query_modal_form');
    $sqlManagerForm.find('textarea[name="sql"]').val(query);
    $sqlManagerForm.submit();
  }
}
