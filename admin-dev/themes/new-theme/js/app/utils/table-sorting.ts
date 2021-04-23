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

const {$} = window;

/**
 * Makes a table sortable by columns.
 * This forces a page reload with more query parameters.
 */
class TableSorting {
  selector: string;

  columns: JQuery;

  /**
   * @param {jQuery} table
   */
  constructor(table: JQuery) {
    this.selector = '.ps-sortable-column';
    this.columns = $(table).find(this.selector);
  }

  /**
   * Attaches the listeners
   */
  attach(): void {
    this.columns.on('click', (e) => {
      const $column = $(e.delegateTarget);
      this.sortByColumn($column, this.getToggledSortDirection($column));
    });
  }

  /**
   * Sort using a column name
   * @param {string} columnName
   * @param {string} direction "asc" or "desc"
   */
  sortBy(columnName: string, direction: string): void {
    const $column = this.columns.is(`[data-sort-col-name="${columnName}"]`);

    if (!$column) {
      throw new Error(`Cannot sort by "${columnName}": invalid column`);
    }

    this.sortByColumn(this.columns, direction);
  }

  /**
   * Sort using a column element
   * @param {jQuery} column
   * @param {string} direction "asc" or "desc"
   * @private
   */
  private sortByColumn(column: JQuery, direction: string): void {
    window.location.href = this.getUrl(
      column.data('sortColName'),
      direction === 'desc' ? 'desc' : 'asc',
      column.data('sortPrefix'),
    );
  }

  /**
   * Returns the inverted direction to sort according to the column's current one
   * @param {jQuery} column
   * @return {string}
   * @private
   */
  private getToggledSortDirection(column: JQuery): string {
    return column.data('sortDirection') === 'asc' ? 'desc' : 'asc';
  }

  /**
   * Returns the url for the sorted table
   * @param {string} colName
   * @param {string} direction
   * @param {string} prefix
   * @return {string}
   * @private
   */
  private getUrl(colName: string, direction: string, prefix: string): string {
    const url = new URL(window.location.href);
    const params = url.searchParams;

    if (prefix) {
      params.set(`${prefix}[orderBy]`, colName);
      params.set(`${prefix}[sortOrder]`, direction);
    } else {
      params.set('orderBy', colName);
      params.set('sortOrder', direction);
    }

    return url.toString();
  }
}

export default TableSorting;
