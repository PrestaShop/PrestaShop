/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Makes a table sortable by columns.
 * This forces a page reload with more query parameters.
 */
class TableSorting {
  selector: string;

  idTable: string;

  columns: JQuery;

  /**
   * @param {jQuery} table
   */
  constructor(table: JQuery) {
    this.selector = '.ps-sortable-column';
    this.idTable = table.attr('id') ?? '';
    this.columns = table.find(this.selector);
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
    url.hash = this.idTable;

    return url.toString();
  }
}

export default TableSorting;
