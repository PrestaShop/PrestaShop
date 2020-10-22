require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Statuses extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Statuses •';

    // Header selectors
    this.newOrderStatusLink = '#page-header-desc-order_return_state-new_order_state';
    this.newOrderReturnStatusLink = '#page-header-desc-order_return_state-new_order_return_state';

    // Selectors
    // Form selectors
    this.gridForm = tableName => `#form-${tableName}_state`;
    this.gridTableHeaderTitle = tableName => `${this.gridForm(tableName)} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = tableName => `${this.gridTableHeaderTitle(tableName)} span.badge`;

    // Table selectors
    this.gridTable = tableName => `#table-${tableName}_state`;

    // Filter selectors
    this.filterRow = tableName => `${this.gridTable(tableName)} tr.filter`;
    this.filterColumn = (tableName, filterBy) => `${this.filterRow(tableName)}
    [name='${tableName}_stateFilter_${filterBy}']`;
    this.filterSearchButton = tableName => `#submitFilterButton${tableName}_state`;
    this.filterResetButton = tableName => `button[name='submitReset${tableName}_state']`;

    // Table body selectors
    this.tableBody = tableName => `${this.gridTable(tableName)} tbody`;
    this.tableBodyRows = tableName => `${this.tableBody(tableName)} tr`;
    this.tableBodyRow = (tableName, row) => `${this.tableBodyRows(tableName)}:nth-child(${row})`;
    this.tableBodyColumns = (tableName, row) => `${this.tableBodyRow(tableName, row)} td`;

    // Columns selectors
    this.tableColumn = (tableName, row, column) => `${this.tableBodyColumns(tableName, row)}:nth-child(${column})`;

    // Row actions selectors
    this.tableColumnActions = (tableName, row) => `${this.tableBodyColumns(tableName, row)}
    .btn-group-action`;
    this.tableColumnActionsEditLink = (tableName, row) => `${this.tableColumnActions(tableName, row)} a.edit`;
    this.tableColumnActionsToggleButton = (tableName, row) => `${this.tableColumnActions(tableName, row)}
    button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (tableName, row) => `${this.tableColumnActions(tableName, row)}
    .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (tableName, row) => `${this.tableColumnActionsDropdownMenu(tableName, row)}
    a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Pagination selectors
    this.paginationActiveLabel = tableName => `${this.gridForm(tableName)} ul.pagination.pull-right li.active a`;
    this.paginationDiv = tableName => `${this.gridForm(tableName)} .pagination`;
    this.paginationDropdownButton = tableName => `${this.paginationDiv(tableName)} .dropdown-toggle`;
    this.paginationItems = (tableName, number) => `${this.gridForm(tableName)}
    .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = tableName => `${this.gridForm(tableName)} .icon-angle-left`;
    this.paginationNextLink = tableName => `${this.gridForm(tableName)} .icon-angle-right`;

    // Sort Selectors
    this.tableHead = tableName => `${this.gridTable(tableName)} thead`;
    this.sortColumnDiv = (tableName, column) => `${this.tableHead(tableName)} th:nth-child(${column})`;
    this.sortColumnSpanButton = (tableName, column) => `${this.sortColumnDiv(tableName, column)} span.ps-sort`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = tableName => `#bulk_action_menu_${tableName}_state`;
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
  }

  /* Statuses methods */

  /* Header methods */

  /**
   * Go to new orders status page
   * @param page
   * @return {Promise<void>}
   */
  async goToNewOrderStatusPage(page) {
    await this.clickAndWaitForNavigation(page, this.newOrderStatusLink);
  }

  /**
   * Go to new orders return status page
   * @param page
   * @return {Promise<void>}
   */
  async goToNewOrderReturnStatusPage(page) {
    await this.clickAndWaitForNavigation(page, this.newOrderReturnStatusLink);
  }

  /* Filter methods */

  /**
   * Get Number of order statuses
   * @param page
   * @param tableName
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page, tableName = 'order') {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan(tableName));
  }

  /**
   * Reset all filters
   * @param page
   * @param tableName
   * @return {Promise<void>}
   */
  async resetFilter(page, tableName = 'order') {
    if (!(await this.elementNotVisible(page, this.filterResetButton(tableName), 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton(tableName));
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton(tableName), 2000);
  }

  /**
   * Reset and get number of lines
   * @param page
   * @param tableName
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page, tableName = 'order') {
    await this.resetFilter(page, tableName);

    return this.getNumberOfElementInGrid(page, tableName);
  }

  /**
   * Filter table
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
   * @param tableName
   * @return {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value, tableName = 'order') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(tableName, filterBy), value.toString());
        await this.clickAndWaitForNavigation(page, this.filterSearchButton(tableName));
        break;

      case 'select':
        await Promise.all([
          page.waitForNavigation({waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(tableName, filterBy), value ? 'Yes' : 'No'),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /* Column methods */

  /**
   * Get text from column in table
   * @param page
   * @param row
   * @param columnName
   * @param column
   * @param tableName
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName, column, tableName = 'order') {
    if (columnName === 'send_email' || columnName === 'delivery' || columnName === 'invoice') {
      return this.getAttributeContent(page, `${this.tableColumn(tableName, row, column)} a`, 'title');
    }
    return this.getTextContent(page, this.tableColumn(tableName, row, column));
  }

  /**
   * Go to edit page
   * @param page
   * @param row
   * @param tableName
   * @return {Promise<void>}
   */
  async gotoEditPage(page, row, tableName = 'order') {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(tableName, row));
  }

  /**
   * Delete order status from row
   * @param page
   * @param row
   * @param tableName
   * @return {Promise<string>}
   */
  async deleteOrderStatus(page, row, tableName = 'order') {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(tableName, row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(tableName, row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(tableName, row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page
   * @param tableName
   * @return {Promise<string>}
   */
  getPaginationLabel(page, tableName = 'order') {
    return this.getTextContent(page, this.paginationActiveLabel(tableName));
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
   * @param tableName
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number, tableName = 'order') {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton(tableName));
    await this.clickAndWaitForNavigation(page, this.paginationItems(tableName, number));

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on next
   * @param page
   * @param tableName
   * @returns {Promise<string>}
   */
  async paginationNext(page, tableName = 'order') {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on previous
   * @param page
   * @param tableName
   * @returns {Promise<string>}
   */
  async paginationPrevious(page, tableName = 'order') {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page
   * @param columnName
   * @param columnID
   * @param tableName
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, columnName, columnID, tableName = 'order') {
    const rowsNumber = await this.getNumberOfElementInGrid(page, tableName);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName, columnID, tableName);
      await allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param page
   * @param sortBy, column to sort with
   * @param columnID, id column
   * @param sortDirection, asc or desc
   * @param tableName
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, columnID, sortDirection, tableName = 'order') {
    const sortColumnButton = `${this.sortColumnDiv(tableName, columnID)} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForNavigation(page, sortColumnButton);
  }

  /* Bulk actions methods */
  /**
   * Select all rows
   * @param page
   * @param tableName
   * @return {Promise<void>}
   */
  async bulkSelectRows(page, tableName = 'order') {
    await page.click(this.bulkActionMenuButton(tableName));

    await Promise.all([
      page.click(this.selectAllLink),
      page.waitForSelector(this.selectAllLink, {state: 'hidden'}),
    ]);
  }

  /**
   * Delete order statuses by bulk action
   * @param page
   * @param tableName
   * @returns {Promise<string>}
   */
  async bulkDeleteOrderStatuses(page, tableName = 'order') {
    this.dialogListener(page, true);
    // Select all rows
    await this.bulkSelectRows(page, tableName);

    // Click on Button Bulk actions
    await page.click(this.bulkActionMenuButton(tableName));

    // Click on delete
    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new Statuses();
