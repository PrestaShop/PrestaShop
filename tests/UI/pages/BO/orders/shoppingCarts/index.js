require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Shopping carts page, contains functions that can be used on shopping carts page
 * @class
 * @extends BOBasePage
 */
class ShoppingCarts extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on shopping carts page
   */
  constructor() {
    super();

    this.pageTitle = 'Shopping Carts •';
    this.alertSuccessBlockParagraph = '.alert-success';

    // Form selectors
    this.gridForm = '#form-cart';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-cart';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='cartFilter_${filterBy}']`;
    this.filterDateFromColumn = `${this.filterRow} #local_cartFilter_a__date_add_0`;
    this.filterDateToColumn = `${this.filterRow} #local_cartFilter_a__date_add_1`;
    this.filterSearchButton = '#submitFilterButtoncart';
    this.filterResetButton = 'button[name=\'submitResetcart\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(1)`;
    this.tableColumnOrderId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnCustomer = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnCarrier = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnDate = row => `${this.tableBodyColumn(row)}:nth-child(6)`;
    this.tableColumnOnline = row => `${this.tableBodyColumn(row)}:nth-child(7)`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_cart';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = number => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /* Filter methods */

  /**
   * Get Number of shopping carts
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Reset and get number of shopping carts
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter shopping carts
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter (input/select)
   * @param filterBy {string} Column to filter with
   * @param value {string} Value to filter
   * @returns {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
        await this.clickAndWaitForNavigation(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForNavigation({waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(filterBy), value ? 'Yes' : 'No'),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /**
   * Filter by date
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on filter date from input
   * @param dateTo {string} Value to set on filter date to input
   * @returns {Promise<void>}
   */
  async filterByDate(page, dateFrom, dateTo) {
    await page.type(this.filterDateFromColumn, dateFrom);
    await page.type(this.filterDateToColumn, dateTo);
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Shopping cart row on table
   * @param columnName {string} Column name of the value to return
   * @returns {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_cart':
        columnSelector = this.tableColumnId(row);
        break;

      case 'status':
        columnSelector = this.tableColumnOrderId(row);
        break;

      case 'c!lastname':
        columnSelector = this.tableColumnCustomer(row);
        break;

      case 'ca!name':
        columnSelector = this.tableColumnCarrier(row);
        break;

      case 'date':
        columnSelector = this.tableColumnDate(row);
        break;

      case 'id_guest':
        columnSelector = this.tableColumnOnline(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, columnName) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /* Bulk actions methods */
  /**
   * Bulk delete shopping carts
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async bulkDeleteShoppingCarts(page) {
    // To confirm bulk delete action with dialog
    this.dialogListener(page, true);

    // Select all rows
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.selectAllLink),
    ]);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);

    // Perform delete
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Pagination limit number to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.waitForSelectorAndClick(page, this.paginationItems(number));
    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);
    return this.getPaginationLabel(page);
  }

  // Sort methods
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column name to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @returns {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    let columnSelector;

    switch (sortBy) {
      case 'id_cart':
        columnSelector = this.sortColumnDiv(1);
        break;

      case 'status':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'c!lastname':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'ca!name':
        columnSelector = this.sortColumnDiv(5);
        break;

      case 'date':
        columnSelector = this.sortColumnDiv(6);
        break;

      case 'id_guest':
        columnSelector = this.sortColumnDiv(7);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForNavigation(page, sortColumnButton);
  }
}

module.exports = new ShoppingCarts();
