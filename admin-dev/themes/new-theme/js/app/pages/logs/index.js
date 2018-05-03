/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import TableSorting from '../../utils/table-sorting';
import initDatePickers from '../../utils/datepicker';
import SqlManager from '../../utils/sql-manager';
import Grid from '../../../components/grid';

const $ = global.$;

class LogsPage {

  init() {
    new Grid('#logs_grid_panel').init();

    const $sortableTables = $('table.table');
    const $deleteAllLogsButton = $('#logs-deleteAll');
    const $refreshButton = $('#logs-refresh');
    const $showSqlQueryButton = $('#logs-showSqlQuery');
    const $exportSqlManagerButton = $('#logs-exportSqlManager');

    this.sqlManager = new SqlManager();

    new TableSorting($sortableTables).attach();
    initDatePickers();

    $deleteAllLogsButton.on('click', this._onDeleteAllLogsClick.bind(this));
    $refreshButton.on('click', this._onRefreshClick.bind(this));
    $showSqlQueryButton.on('click', this._onShowSqlQueryClick.bind(this));
    $exportSqlManagerButton.on('click', this._onExportSqlManagerClick.bind(this));
  }

  /**
   * Invoked when clicking on the "delete all logs" toolbar button
   * @param {jQuery.Event} event
   * @private
   */
  _onDeleteAllLogsClick(event) {
    const clickedButton = $(event.delegateTarget);
    const confirmationMessage = clickedButton.data('confirmMessage');
    const form = clickedButton.closest('form');
    if (global.confirm(confirmationMessage)) {
      form.submit();
    }
  }

  /**
   * Invoked when clicking on the "reload" toolbar button
   * @private
   */
  _onRefreshClick() {
    location.reload();
  }

  /**
   * Invoked when clicking on the "show sql query" toolbar button
   * @private
   */
  _onShowSqlQueryClick() {
    this.sqlManager.showLastSqlQuery();
  }

  /**
   * Invoked when clicking on the "export to the sql query" toolbar button
   * @private
   */
  _onExportSqlManagerClick() {
    this.sqlManager.sendLastSqlQuery(this.sqlManager.createSqlQueryName());
  }
}

$(() => {
  new LogsPage().init();
});
