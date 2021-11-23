require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * States page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class States extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on states page
   */
  constructor() {
    super();

    this.pageTitle = 'States •';

    // Header selectors
    this.addNewStateLink = '#page-header-desc-state-new_state';

    // Form selectors
    this.gridForm = '#form-state';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-state';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='stateFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonstate';
    this.filterResetButton = 'button[name=\'submitResetstate\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnIsoCode = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnZone = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnCountry = row => `${this.tableBodyColumn(row)}:nth-child(6)`;
    this.tableColumnStatusLink = row => `${this.tableBodyColumn(row)}:nth-child(7) a`;
    this.tableColumnStatusEnableLink = row => `${this.tableColumnStatusLink(row)}.action-enabled`;
    this.tableColumnStatusDisableLink = row => `${this.tableColumnStatusLink(row)}.action-disabled`;

    // Column actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.columnActionsEditLink = row => `${this.tableColumnActions(row)} a.edit`;
    this.columnActionsDropdownButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.columnActionsDeleteLink = row => `${this.tableColumnActions(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_state';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkEnableLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
    this.bulkDisableLink = `${this.bulkActionDropdownMenu} li:nth-child(5)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(7)`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = number => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;
  }

  /* Header methods */
  /**
   * Go To add new state page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewStatePage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewStateLink);
  }

  /* Filter Methods */
  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Get number of states
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset and get number of states
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter states
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterStates(page, filterType, filterBy, value) {
    let filterValue = value;

    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), filterValue.toString());
        await this.clickAndWaitForNavigation(page, this.filterSearchButton);
        break;

      case 'select':
        if (typeof value === 'boolean') {
          filterValue = value ? 'Yes' : 'No';
        }

        await Promise.all([
          this.selectByVisibleText(page, this.filterColumn(filterBy), filterValue),
          page.waitForNavigation({waitUntil: 'networkidle'}),
        ]);

        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column to get text value
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_state':
        columnSelector = this.tableColumnId(row);
        break;

      case 'a!name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'iso_code':
        columnSelector = this.tableColumnIsoCode(row);
        break;

      case 'z!id_zone':
        columnSelector = this.tableColumnZone(row);
        break;

      case 'cl!id_country':
        columnSelector = this.tableColumnCountry(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get state status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  getStateStatus(page, row) {
    return this.elementVisible(page, this.tableColumnStatusEnableLink(row), 1000);
  }

  /**
   * Set state status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param wantedStatus {boolean} True if we need to enable status, false if not
   * @return {Promise<void>}
   */
  async setStateStatus(page, row, wantedStatus) {
    if (wantedStatus !== await this.getStateStatus(page, row)) {
      await this.clickAndWaitForNavigation(page, this.tableColumnStatusLink(row));
    }
  }


  /**
   * Go to edit state page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditStatePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.columnActionsEditLink(row));
  }

  /**
   * Delete state
   * @param page {Page} Browser tab
   * @param row {number} Row on table to delete
   * @return {Promise<string>}
   */
  async deleteState(page, row) {
    // Open dropdown link list
    await page.click(this.columnActionsDropdownButton(row));

    // Click on delete link
    await page.click(this.columnActionsDeleteLink(row));

    // Confirm delete in modal
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all rows content
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

  /* Bulk actions methods */

  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async bulkSelectRows(page) {
    await page.click(this.bulkActionMenuButton);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);
  }

  /**
   * Bulk delete states
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async bulkDeleteStates(page) {
    // To confirm bulk delete action with dialog
    this.dialogListener(page, true);

    // Select all rows
    await this.bulkSelectRows(page);

    // Perform delete
    await page.click(this.bulkActionMenuButton);
    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Bulk set states status
   * @param page {Page} Browser tab
   * @param wantedStatus {boolean} True if we need to bulk enable status, false if not
   * @return {Promise<void>}
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

  /* Sort table method */

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
      case 'id_state':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'a!name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'iso_code':
        columnSelector = this.sortColumnDiv(4);
        break;

      case 'z!id_zone':
        columnSelector = this.sortColumnDiv(5);
        break;

      case 'cl!id_country':
        columnSelector = this.sortColumnDiv(6);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForNavigation(page, sortColumnButton);
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
}

module.exports = new States();
