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

import resetSearch from '../app/utils/reset_search';
import TableSorting from '../app/utils/table-sorting';
import datePicker from '../app/utils/datepicker';

const $ = window.$;

/**
 * Class is responsible for handling Grid events
 */
export default class Grid {
  /**
   * Grid's selector
   *
   * @param gridSelector
   */
  constructor(gridSelector) {
    this.$grid = $(gridSelector);
  }

  /**
   * Initialize grid events
   */
  init() {
    this._handleBulkActionSelectAllCheckbox();
    this._handleBulkActionCheckboxSelect();
    this._handleCommonGridActions();
    this._handleSortingGrid();
    this._enableDatePickers();
  }

  /**
   * Handles most common grid actions (show sql, refresh list & etc.)
   *
   * @private
   */
  _handleCommonGridActions() {
    let identifier = this.$grid.find('.js-grid').attr('id');
    let commonActionSuffix = '#' + identifier + '_action_';

    let refreshListActionId = commonActionSuffix + 'common_refresh_list';
    let showSqlActionId = commonActionSuffix + 'common_show_query';
    let exportSqlManagerActionId = commonActionSuffix + 'common_export_sql_manager';

    this.$grid.on('click', refreshListActionId, () => this._onRefreshClick());
    this.$grid.on('click', showSqlActionId, () => this._onShowSqlQueryClick());
    this.$grid.on('click', exportSqlManagerActionId, () => this._onExportSqlManagerClick());

    $('.reset-search').on('click', (event) => {
      resetSearch($(event.target).data('url'), $(event.target).data('redirect'));
    });
  }

  /**
   * Handles the column sorting using Table component
   *
   * @private
   */
  _handleSortingGrid() {
    const $sortableTable = this.$grid.find('table.table');
    new TableSorting($sortableTable).attach();
  }

  /**
   * If any, enable Date pickers component on date inputs.
   */
  _enableDatePickers() {
    datePicker();
  }

  /**
   * Handles "Select all" button in the grid
   *
   * @private
   */
  _handleBulkActionSelectAllCheckbox() {
    $(document).on('change', '.js-bulk-action-select-all', (e) => {
      const $checkbox = $(e.target);

      const isChecked = $checkbox.is(':checked');
      if (isChecked) {
        this._enableBulkActionsBtn();
      } else {
        this._disableBulkActionsBtn();
      }

      this.$grid.find('.js-bulk-action-checkbox').prop('checked', isChecked);
    });
  }

  /**
   * Handles each bulk action checkbox select in the grid
   *
   * @private
   */
  _handleBulkActionCheckboxSelect() {
    this.$grid.on('change', '.js-bulk-action-checkbox', () => {
      const checkedRowsCount = this.$grid.find('.js-bulk-action-checkbox:checked').length;

      if (checkedRowsCount > 0) {
        this._enableBulkActionsBtn();
      } else {
        this._disableBulkActionsBtn();
      }
    });
  }

  /**
   * Enable bulk actions button
   *
   * @private
   */
  _enableBulkActionsBtn() {
    this.$grid.find('.js-bulk-actions-btn').prop('disabled', false);
  }

  /**
   * Disable bulk actions button
   *
   * @private
   */
  _disableBulkActionsBtn() {
    this.$grid.find('.js-bulk-actions-btn').prop('disabled', true);
  }

  /**
   * Invoked when clicking on the "reload" toolbar button
   *
   * @private
   */
  _onRefreshClick() {
    location.reload();
  }

  /**
   * Invoked when clicking on the "show sql query" toolbar button
   *
   * @private
   */
  _onShowSqlQueryClick() {
    let identifier = this.$grid.find('.js-grid').attr('id');
    let query = this.$grid.find('.js-grid-table').data('query');

    const $sqlManagerForm = $('#' + identifier + '_common_show_query_modal_form');
    $sqlManagerForm.find('textarea[name="sql"]').val(query);

    const $modal = $('#' + identifier + '_common_show_query_modal');
    $modal.modal('show');

    $modal.on('click', '.btn-sql-submit', () => $sqlManagerForm.submit());
  }

  /**
   * Invoked when clicking on the "export to the sql query" toolbar button
   *
   * @private
   */
  _onExportSqlManagerClick() {
    let identifier = this.$grid.find('.js-grid').attr('id');
    let query = this.$grid.find('.js-grid-table').data('query');

    const $sqlManagerForm = $('#' + identifier + '_common_show_query_modal_form');
    $sqlManagerForm.find('textarea[name="sql"]').val(query);
    $sqlManagerForm.submit();
  }
}
