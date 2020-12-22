require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Countries extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Countries •';

    // Selectors
    // Header selectors
    this.addNewCountryButton = '#page-header-desc-country-new_country';

    // Form selectors
    this.gridForm = '#form-country';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;
    this.gridTable = '#table-country';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='countryFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtoncountry';
    this.filterResetButton = 'button[name=\'submitResetcountry\']';

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td:nth-child(${column})`;

    // Columns selectors
    this.tableColumnId = row => this.tableColumn(row, 2);
    this.tableColumnName = row => this.tableColumn(row, 3);
    this.tableColumnIsoCode = row => this.tableColumn(row, 4);
    this.tableColumnCallPrefix = row => this.tableColumn(row, 5);
    this.tableColumnZone = row => this.tableColumn(row, 6);
    this.tableColumnStatusLink = row => `${this.tableColumn(row, 7)} a`;
    this.tableColumnStatusEnableLink = row => `${this.tableColumnStatusLink(row)}.action-enabled`;
    this.tableColumnStatusDisableLink = row => `${this.tableColumn(row)}.action-disabled`;

    // Actions selectors
    this.editRowLink = row => `${this.tableRow(row)} a.edit`;

    // Bulk Actions
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = `${this.gridForm} button.dropdown-toggle`;
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

  /*
  Methods
   */
  /**
   * Go to add new country page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewCountryPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewCountryButton);
  }

  /**
   * Reset filter
   * @param page
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Get number of element in grid
   * @param page
   * @returns {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset and get number of lines
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Go to edit country page
   * @param page
   * @param countryID
   * @returns {Promise<void>}
   */
  async goToEditCountryPage(page, countryID = 1) {
    await this.clickAndWaitForNavigation(page, this.editRowLink(countryID));
  }

  /**
   * Get text column from table
   * @param page
   * @param row
   * @param columnName
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_country':
        columnSelector = this.tableColumnId(row);
        break;

      case 'b!name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'iso_code':
        columnSelector = this.tableColumnIsoCode(row);
        break;

      case 'call_prefix':
        columnSelector = this.tableColumnCallPrefix(row);
        break;

      case 'z!id_zone':
        columnSelector = this.tableColumnZone(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Filter table
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
   * @returns {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value) {
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

  /**
   * Get country status
   * @param page
   * @param row
   * @return {Promise<boolean>}
   */
  getCountryStatus(page, row) {
    return this.elementVisible(page, this.tableColumnStatusEnableLink(row), 1000);
  }

  /**
   * Set country status
   * @param page
   * @param row
   * @param wantedStatus
   * @return {Promise<void>}
   */
  async setCountryStatus(page, row, wantedStatus) {
    if (wantedStatus !== await this.getCountryStatus(page, row)) {
      await this.clickAndWaitForNavigation(page, this.tableColumnStatusLink(row));
    }
  }

  /**
   * Get content from all rows
   * @param page
   * @param columnName
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, columnName) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      let rowContent = await this.getTextColumnFromTable(page, i, columnName);

      if (rowContent === '-') {
        rowContent = '0';
      }

      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /* Bulk actions methods */

  /**
   * Select all rows
   * @param page
   * @return {Promise<void>}
   */
  async bulkSelectRows(page) {
    await page.click(this.bulkActionMenuButton);

    await Promise.all([
      page.click(this.selectAllLink),
      page.waitForSelector(this.selectAllLink, {state: 'hidden'}),
    ]);
  }

  /**
   * Delete countries by bulk action
   * @param page
   * @returns {Promise<string>}
   */
  async deleteCountriesByBulkActions(page) {
    this.dialogListener(page, true);
    // Select all rows
    await this.bulkSelectRows(page);

    // Click on Button Bulk actions
    await page.click(this.bulkActionMenuButton);

    // Click on delete
    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Bulk set status
   * @param page
   * @param wantedStatus
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
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    let columnSelector;

    switch (sortBy) {
      case 'id_country':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'b!name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'iso_code':
        columnSelector = this.sortColumnDiv(4);
        break;

      case 'call_prefix':
        columnSelector = this.sortColumnDiv(5);
        break;

      case 'z!id_zone':
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
   * @param page
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.clickAndWaitForNavigation(page, this.paginationItems(number));

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }
}

module.exports = new Countries();
