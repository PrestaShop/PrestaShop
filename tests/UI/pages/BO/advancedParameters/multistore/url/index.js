require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Shop url page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ShopURLSettings extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on shop url page
   */
  constructor() {
    super();

    this.successUpdateMessage = 'The status has been successfully updated.';
    this.alertSuccessBlockParagraph = '.alert-success';

    // Header selectors
    this.addNewUrlButton = '#page-header-desc-shop_url-new';

    // Form selectors
    this.gridForm = '#form-shop_url';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-shop_url';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;
    this.tableColumn = (row, column) => `${this.tableBodyRow(row)} td:nth-child(${column})`;
    this.columnValidIcon = (row, column) => `${this.tableColumn(row, column)} a[title='Enabled']`;
    this.columnNotValidIcon = (row, column) => `${this.tableColumn(row, column)} a[title='Disabled']`;

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='shop_urlFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonshop_url';
    this.filterResetButton = 'button[name=\'submitResetshop_url\']';

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

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

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_shop_url';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkEnableLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
    this.bulkDisableLink = `${this.bulkActionDropdownMenu} li:nth-child(5)`;
  }

  /* Methods */

  /**
   * Go to add new url page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewUrl(page) {
    await this.clickAndWaitForNavigation(page, this.addNewUrlButton);
  }

  /* Filter methods */

  /**
   * Get number of elements
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Reset and get number of shops
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter table
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
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
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name to get text content
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_shop_url':
        columnSelector = this.tableColumn(row, 2);
        break;

      case 's!name':
        columnSelector = this.tableColumn(row, 3);
        break;

      case 'url':
        columnSelector = this.tableColumn(row, 4);
        break;

      case 'main':
        columnSelector = `${this.tableColumn(row, 5)} a`;
        break;

      case 'active':
        columnSelector = `${this.tableColumn(row, 6)} a`;
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    if ((columnName === 'active') || (columnName === 'main')) {
      return this.getAttributeContent(page, columnSelector, 'title');
    }
    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get text content
   * @return {Promise<Array<string>>}
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

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.clickAndWaitForNavigation(page, this.paginationItems(number));

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

  /**
   * Delete shop URL
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteShopURL(page, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Sort methods */
  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    let columnSelector;

    switch (sortBy) {
      case 'id_shop_url':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 's!name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'url':
        columnSelector = this.sortColumnDiv(4);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }
    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForNavigation(page, sortColumnButton);
  }

  // Quick edit methods
  /**
   * Get value of column displayed in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get status value
   * @return {Promise<boolean>}
   */
  async getStatus(page, row, column) {
    return this.elementVisible(page, this.columnValidIcon(row, column), 100);
  }

  /**
   * Quick edit toggle column value in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to set status value
   * @param valueWanted {boolean} True if we need to enable status, false if not
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page, row, column, valueWanted = true) {
    await this.waitForVisibleSelector(page, this.tableColumn(row, column), 2000);
    if (await this.getStatus(page, row, column) !== valueWanted) {
      await page.click(this.tableColumn(row, column));
      await this.waitForVisibleSelector(
        page,
        (valueWanted ? this.columnValidIcon : this.columnNotValidIcon)(row, column),
      );
      return true;
    }

    return false;
  }

  // Bulk actions methods
  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async bulkSelectRows(page) {
    await page.click(this.bulkActionMenuButton);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);
  }

  /**
   * Enable/Disable shop url
   * @param page {Page} Browser tab
   * @param wantedStatus {boolean} True if we need to bulk enable status, false if not
   * @returns {Promise<void>}
   */
  async bulkSetStatus(page, wantedStatus) {
    // Select all rows
    await this.bulkSelectRows(page);

    // Set status
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkEnableLink),
    ]);

    await this.clickAndWaitForNavigation(
      page,
      wantedStatus ? this.bulkEnableLink : this.bulkDisableLink,
    );
  }
}

module.exports = new ShopURLSettings();
