import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Search page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Search extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  public readonly settingsUpdateMessage: string;

  private readonly addNewAliasLink: string;

  private readonly tagsTabLink: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfTitlesSpan: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableBodyRows: string;

  private readonly tableBodyRow: (row: number) => string;

  private readonly tableBodyColumn: (row: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsEditLink: (row: number) => string;

  private readonly tableColumnActionsToggleButton: (row: number) => string;

  private readonly tableColumnActionsDropdownMenu: (row: number) => string;

  private readonly tableColumnActionsDeleteLink: (row: number) => string;

  private readonly deleteModalButtonYes;

  private readonly tableColumnAliases: (row: number) => string;

  private readonly tableColumnSearch: (row: number) => string;

  private readonly tableColumnStatus: (row: number) => string;

  private readonly tableColumnStatusEnabledIcon: (row: number) => string;

  private readonly tableColumnStatusDisabledIcon: (row: number) => string;

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkDeleteLink: string;

  private readonly bulkEnableButton: string;

  private readonly bulkDisableButton: string;

  private readonly paginationDiv: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationListOpen: string;

  private readonly paginationNumber: (number: number) => string;

  private readonly paginationRightBlock: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string, direction: string) => string;

  private readonly aliasForm: string;

  private readonly fuzzySearchLabel: (status: string) => string;

  private readonly saveFormButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on search page
   */
  constructor() {
    super();

    this.pageTitle = 'Search •';
    this.successfulCreationMessage = 'Successful creation';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';
    this.successfulUpdateMessage = 'Update successful';
    this.settingsUpdateMessage = 'The settings have been successfully updated.';

    // Selectors
    // Header links
    this.addNewAliasLink = 'a[data-role=page-header-desc-alias-link]';

    // Tabs
    this.tagsTabLink = '#subtab-AdminTags';

    // Form selectors
    this.gridForm = '#form-alias';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-alias';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='aliasFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonalias';
    this.filterResetButton = 'button[name=\'submitResetalias\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;

    // Row actions selectors
    this.tableColumnActions = (row: number) => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = (row: number) => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = (row: number) => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (row: number) => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (row: number) => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Columns selectors
    this.tableColumnAliases = (row: number) => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnSearch = (row: number) => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnStatus = (row: number) => `${this.tableBodyColumn(row)}:nth-child(4) a`;
    this.tableColumnStatusEnabledIcon = (row: number) => `${this.tableColumnStatus(row)}.action-enabled`;
    this.tableColumnStatusDisabledIcon = (row: number) => `${this.tableColumnStatus(row)}.action-disabled`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_alias';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(7)`;
    this.bulkEnableButton = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
    this.bulkDisableButton = `${this.bulkActionDropdownMenu} li:nth-child(5)`;

    // Pagination selectors
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationLimitSelect = `${this.paginationDiv}  button.dropdown-toggle`;
    this.paginationListOpen = `${this.paginationDiv}.open`;
    this.paginationNumber = (number: number) => `${this.gridForm} div.row li a[data-items='${number}']`;
    this.paginationRightBlock = `${this.paginationDiv}.pull-right`;
    this.paginationLabel = `${this.paginationRightBlock} li.active a`;
    this.paginationNextLink = `${this.paginationRightBlock} i.icon-angle-right`;
    this.paginationPreviousLink = `${this.paginationRightBlock} i.icon-angle-left`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: string, direction: string) => `${this.tableHead} a.${direction}-sort-column-${column}-link`;

    // Search form
    this.aliasForm = '#alias_fieldset_search';
    this.fuzzySearchLabel = (status: string) => `#PS_SEARCH_FUZZY_${status}`;
    this.saveFormButton = `${this.aliasForm} button[name='submitOptionsalias']`;
  }

  /*
  Methods
   */

  /* Header methods */
  /**
   * Go to add new alias page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewAliasPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewAliasLink);
  }

  /**
   * Go to tags page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToTagsPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.tagsTabLink);
  }

  /* Filter methods */
  /**
   * Get number of lines
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForURL(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Reset and get number of lines
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter aliases
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter (input/select)
   * @param filterBy {string} Column to filter with
   * @param value {string} Value to filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    const currentUrl: string = page.url();

    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        await this.clickAndWaitForURL(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(filterBy), value === '1' ? 'Yes' : 'No'),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /* Column methods */
  /**
   * Go to edit page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async gotoEditAliasPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name of the value to return
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    let columnSelector;

    switch (columnName) {
      case 'alias':
        columnSelector = this.tableColumnAliases(row);
        break;

      case 'search':
        columnSelector = this.tableColumnSearch(row);
        break;

      case 'active':
        columnSelector = this.tableColumnStatus(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    if (columnName === 'active') {
      return this.getAttributeContent(page, columnSelector, 'title');
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all text content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, columnName: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Delete alias from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteAlias(page: Page, row: number): Promise<string> {
    await Promise.all([
      page.locator(this.tableColumnActionsToggleButton(row)).click(),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.locator(this.tableColumnActionsDeleteLink(row)).click();

    // Confirm delete action
    await this.clickAndWaitForURL(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /* Bulk actions methods */
  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async bulkSelectRows(page: Page): Promise<void> {
    await page.locator(this.bulkActionMenuButton).click();

    await Promise.all([
      page.locator(this.selectAllLink).click(),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);
  }

  /**
   * Delete by bulk action
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async bulkDeleteAliases(page: Page): Promise<string> {
    await this.dialogListener(page, true);
    // Select all rows
    await this.bulkSelectRows(page);

    // Click on Button Bulk actions
    await page.locator(this.bulkActionMenuButton).click();

    // Click on delete
    await this.clickAndWaitForURL(page, this.bulkDeleteLink);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Enable / disable by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable status
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page: Page, enable: boolean = true): Promise<string> {
    // Select all rows
    await this.bulkSelectRows(page);

    // Click on Button Bulk actions
    await page.locator(this.bulkActionMenuButton).click();

    // Click on enable/Disable and wait for modal
    await this.clickAndWaitForURL(page, enable ? this.bulkEnableButton : this.bulkDisableButton);

    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Get alias status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page: Page, row: number): Promise<boolean> {
    return this.elementVisible(page, this.tableColumnStatusEnabledIcon(row), 500);
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} Value wanted in column
   * @returns {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page: Page, row: number, valueWanted: boolean = true): Promise<boolean> {
    await this.waitForVisibleSelector(page, this.tableColumnStatus(row), 2000);
    if (await this.getStatus(page, row) !== valueWanted) {
      await page.locator(this.tableColumnStatus(row)).click();
      await this.waitForVisibleSelector(
        page,
        (valueWanted ? this.tableColumnStatusEnabledIcon(row) : this.tableColumnStatusDisabledIcon(row)),
      );
      return true;
    }
    return false;
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await page.locator(this.paginationLimitSelect).click();
    await this.waitForVisibleSelector(page, this.paginationListOpen);
    await this.clickAndWaitForURL(page, this.paginationNumber(number));

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

  /* Sort methods */
  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    await this.clickAndWaitForURL(page, `${this.sortColumnDiv(sortBy, sortDirection)} i`);
  }

  // Methods for search form
  /**
   * Enable/Disable fuzzy search
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable fuzzy search
   * @returns {Promise<string>}
   */
  async setFuzzySearch(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.fuzzySearchLabel(toEnable ? 'on' : 'off'));
    await this.clickAndWaitForLoadState(page, this.saveFormButton);
    await this.elementNotVisible(page, this.fuzzySearchLabel(!toEnable ? 'on' : 'off'), 2000);

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new Search();
