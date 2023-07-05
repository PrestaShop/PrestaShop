import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Countries page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Countries extends BOBasePage {
  public readonly pageTitle: string;

  public readonly settingsUpdateMessage: string;

  private readonly addNewCountryButton: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfTitlesSpan: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableColumn: (row: number, column: number) => string;

  private readonly tableColumnId: (row: number) => string;

  private readonly tableColumnName: (row: number) => string;

  private readonly tableColumnIsoCode: (row: number) => string;

  private readonly tableColumnCallPrefix: (row: number) => string;

  private readonly tableColumnZone: (row: number) => string;

  private readonly tableColumnStatusLink: (row: number) => string;

  private readonly tableColumnStatusEnableLink: (row: number) => string;

  private readonly tableColumnStatusDisableLink: (row: number) => string;

  private readonly editRowLink: (row: number) => string;

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkEnableLink: string;

  private readonly bulkDisableLink: string;

  private readonly bulkDeleteLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: number) => string;

  private readonly sortColumnSpanButton: (column: number) => string;

  private readonly paginationActiveLabel: string;

  private readonly paginationDiv: string;

  private readonly paginationDropdownButton: string;

  private readonly paginationItems: (number: number) => string;

  private readonly paginationPreviousLink: string;

  private readonly paginationNextLink: string;

  private readonly countryForm: string;

  private readonly enableRestrictCountriesToggleLabel: (toggle: string) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on countries page
   */
  constructor() {
    super();

    this.pageTitle = 'Countries •';
    this.settingsUpdateMessage = 'The settings have been successfully updated.';

    // Selectors
    // Header selectors
    this.addNewCountryButton = 'a[data-role=page-header-desc-country-link]';

    // Form selectors
    this.gridForm = '#form-country';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;
    this.gridTable = '#table-country';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='countryFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtoncountry';
    this.filterResetButton = 'button[name=\'submitResetcountry\']';

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableColumn = (row: number, column: number) => `${this.tableRow(row)} td:nth-child(${column})`;

    // Columns selectors
    this.tableColumnId = (row: number) => this.tableColumn(row, 2);
    this.tableColumnName = (row: number) => this.tableColumn(row, 3);
    this.tableColumnIsoCode = (row: number) => this.tableColumn(row, 4);
    this.tableColumnCallPrefix = (row: number) => this.tableColumn(row, 5);
    this.tableColumnZone = (row: number) => this.tableColumn(row, 6);
    this.tableColumnStatusLink = (row: number) => `${this.tableColumn(row, 7)} a`;
    this.tableColumnStatusEnableLink = (row: number) => `${this.tableColumnStatusLink(row)}.action-enabled`;
    this.tableColumnStatusDisableLink = (row: number) => `${this.tableColumnStatusLink(row)}.action-disabled`;

    // Actions selectors
    this.editRowLink = (row: number) => `${this.tableRow(row)} a.edit`;

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
    this.sortColumnDiv = (column: number) => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = (column: number) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = (number: number) => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Country options selectors
    this.countryForm = '#country_form';
    this.enableRestrictCountriesToggleLabel = (toggle: string) => `${this.countryForm} `
      + `#PS_RESTRICT_DELIVERED_COUNTRIES_${toggle}`;
    this.saveButton = `${this.countryForm} button[name='submitOptionscountry']`;
  }

  /*
  Methods
   */
  /**
   * Go to add new country page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewCountryPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewCountryButton);
  }

  /**
   * Reset filter
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForURL(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Get number of element in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset and get number of lines
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Go to edit country page
   * @param page {Page} Browser tab
   * @param row {number} Row on table to edit
   * @returns {Promise<void>}
   */
  async goToEditCountryPage(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.editRowLink(row));
  }

  /**
   * Get text column from table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name to get text content
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, row: number, columnName: string): Promise<string> {
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
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @returns {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    const currentUrl: string = page.url();
    let textValue: string = value;

    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        await this.clickAndWaitForURL(page, this.filterSearchButton);
        break;

      case 'select':
        if (filterBy === 'a!active') {
          textValue = value === '1' ? 'Yes' : 'No';
        }
        await Promise.all([
          this.selectByVisibleText(page, this.filterColumn(filterBy), textValue),
          page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
        ]);

        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /**
   * Get country status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  getCountryStatus(page: Page, row: number): Promise<boolean> {
    return this.elementVisible(page, this.tableColumnStatusEnableLink(row), 1000);
  }

  /**
   * Set country status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param wantedStatus {boolean} True if we need to enable status, false if not
   * @return {Promise<void>}
   */
  async setCountryStatus(page: Page, row: number, wantedStatus: boolean): Promise<void> {
    if (wantedStatus !== await this.getCountryStatus(page, row)) {
      await page.click(this.tableColumnStatusLink(row));
    }
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, columnName: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      let rowContent = await this.getTextColumnFromTable(page, i, columnName);

      if (rowContent === '-') {
        rowContent = '0';
      }

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
  async bulkSelectRows(page: Page): Promise<void> {
    await page.click(this.bulkActionMenuButton);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);
  }

  /**
   * Delete countries by bulk action
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteCountriesByBulkActions(page: Page): Promise<string> {
    await this.dialogListener(page, true);
    // Select all rows
    await this.bulkSelectRows(page);

    // Click on Button Bulk actions
    await page.click(this.bulkActionMenuButton);

    // Click on delete
    await this.clickAndWaitForURL(page, this.bulkDeleteLink);
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Bulk set status
   * @param page {Page} Browser tab
   * @param wantedStatus {boolean} True if we need to bulk enable status, false if not
   * @return {Promise<void>}
   */
  async bulkSetStatus(page: Page, wantedStatus: boolean): Promise<void> {
    // Select all rows
    await this.bulkSelectRows(page);

    // Set status
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkEnableLink),
    ]);

    await this.clickAndWaitForURL(
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
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
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
    await this.clickAndWaitForURL(page, sortColumnButton);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Pagination number limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.clickAndWaitForURL(page, this.paginationItems(number));

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }

  // Country options
  /**
   * Enable/disable restrict country
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable, false to disable
   * @returns {Promise<string>}
   */
  async setCountriesRestrictions(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.enableRestrictCountriesToggleLabel(toEnable ? 'on' : 'off'));
    await page.click(this.saveButton);
    await this.elementNotVisible(page, this.enableRestrictCountriesToggleLabel(!toEnable ? 'on' : 'off'));

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new Countries();
