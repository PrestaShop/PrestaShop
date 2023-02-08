import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Zones page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Zones extends BOBasePage {
  public readonly pageTitle: string;

  private readonly successfulUpdateStatusMessage: string;

  private readonly countriesSubTab: string;

  private readonly statesSubTab: string;

  private readonly addNewZoneLink: string;

  private readonly zonesGridPanelDiv: string;

  private readonly gridHeaderTitle: string;

  private readonly bulkActionsToggleButton: string;

  private readonly enableSelectionButton: string;

  private readonly disableSelectionButton: string;

  private readonly deleteSelectionButton: string;

  private readonly selectAllLabel: string;

  private readonly zonesGridTable: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly zonesFilterColumnInput: (filterBy: string) => string;

  private readonly resetFilterButton: string;

  private readonly searchFilterButton: string;

  private readonly zonesGridRow: (row: number) => string;

  private readonly zonesGridColumn: (row: number, column: string) => string;

  private readonly zonesGridStatusColumn: (row: number) => string;

  private readonly zonesGridStatusColumnToggleInput: (row: number) => string;

  private readonly zonesGridActionsColumn: (row: number) => string;

  private readonly zonesGridColumnEditLink: (row: number) => string;

  private readonly zonesGridColumnToggleDropdown: (row: number) => string;

  private readonly zonesGridDeleteLink: (row: number) => string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on zones page
   */
  constructor() {
    super();

    this.pageTitle = 'Zones •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';
    this.successfulCreationMessage = 'Successful creation';
    this.successfulUpdateMessage = 'Update successful';
    this.successfulDeleteMessage = 'Successful deletion';

    // Selectors
    // SubTab
    this.countriesSubTab = '#subtab-AdminCountries';
    this.statesSubTab = '#subtab-AdminStates';

    // Header
    this.addNewZoneLink = 'a#page-header-desc-configuration-add';

    // Grid
    this.zonesGridPanelDiv = '#zone_grid_panel';
    this.gridHeaderTitle = `${this.zonesGridPanelDiv} h3.card-header-title`;

    // Bulk actions
    this.bulkActionsToggleButton = `${this.zonesGridPanelDiv} button.js-bulk-actions-btn`;
    this.enableSelectionButton = `${this.zonesGridPanelDiv} #zone_grid_bulk_action_enable_selection`;
    this.disableSelectionButton = `${this.zonesGridPanelDiv} #zone_grid_bulk_action_disable_selection`;
    this.deleteSelectionButton = `${this.zonesGridPanelDiv} #zone_grid_bulk_action_delete_selection`;
    this.selectAllLabel = `${this.zonesGridPanelDiv} #zone_grid tr.column-filters .md-checkbox i`;
    this.zonesGridTable = `${this.zonesGridPanelDiv} #zone_grid_table`;
    this.confirmDeleteModal = '#zone-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Filters
    this.zonesFilterColumnInput = (filterBy: string) => `${this.zonesGridTable} #zone_${filterBy}`;
    this.resetFilterButton = `${this.zonesGridTable} .grid-reset-button`;
    this.searchFilterButton = `${this.zonesGridTable} .grid-search-button`;
    this.zonesGridRow = (row: number) => `${this.zonesGridTable} tbody tr:nth-child(${row})`;
    this.zonesGridColumn = (row: number, column: string) => `${this.zonesGridRow(row)} td.column-${column}`;
    this.zonesGridStatusColumn = (row: number) => `${this.zonesGridColumn(row, 'active')} .ps-switch`;
    this.zonesGridStatusColumnToggleInput = (row: number) => `${this.zonesGridStatusColumn(row)} input`;
    this.zonesGridActionsColumn = (row: number) => this.zonesGridColumn(row, 'actions');
    this.zonesGridColumnEditLink = (row: number) => `${this.zonesGridActionsColumn(row)} a.grid-edit-row-link`;
    this.zonesGridColumnToggleDropdown = (row: number) => `${this.zonesGridActionsColumn(row)} a[data-toggle='dropdown']`;
    this.zonesGridDeleteLink = (row: number) => `${this.zonesGridActionsColumn(row)} a.grid-delete-row-link`;

    // Sort
    this.tableHead = `${this.zonesGridTable} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.zonesGridPanelDiv} .col-form-label`;
    this.paginationNextLink = `${this.zonesGridPanelDiv} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.zonesGridPanelDiv} [data-role='previous-page-link']`;
  }

  /* Header methods */
  /**
   * Go to sub tab countries
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToSubTabCountries(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.countriesSubTab);
  }

  /**
   * Go to sub tab states
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabStates(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.statesSubTab);
  }

  /**
   * Go to add new zone page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewZonePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewZoneLink);
  }

  /* Filter Methods */
  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.resetFilterButton, 2000)) {
      await page.click(this.resetFilterButton);
      await this.elementNotVisible(page, this.resetFilterButton, 2000);
    }
    await this.waitForVisibleSelector(page, this.searchFilterButton, 2000);
  }

  /**
   * Get number of zones
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset and get number of zones
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter zones
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterZones(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.zonesFilterColumnInput(filterBy), value);
        break;

      case 'select':
        await this.selectByVisibleText(page, this.zonesFilterColumnInput(filterBy), value === '1' ? 'Yes' : 'No');
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await this.clickAndWaitForURL(page, this.searchFilterButton);
  }

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name to get text column
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    return this.getTextContent(page, this.zonesGridColumn(row, columnName));
  }

  /**
   * Get zone status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getZoneStatus(page: Page, row: number): Promise<boolean> {
    const inputValue = await this.getAttributeContent(
      page,
      `${this.zonesGridStatusColumnToggleInput(row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Set zone status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param wantedStatus {boolean} True if we need to enable zone status
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setZoneStatus(page: Page, row: number, wantedStatus: boolean): Promise<boolean> {
    if (wantedStatus !== await this.getZoneStatus(page, row)) {
      await page.click(this.zonesGridStatusColumn(row));
      return true;
    }

    return false;
  }

  /**
   * Go to edit zone page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditZonePage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.zonesGridColumnEditLink(row));
  }

  /**
   * Delete zone
   * @param page {Page} Browser tab
   * @param row {Number} Row on table
   * @return {Promise<string>}
   */
  async deleteZone(page: Page, row: number): Promise<string> {
    // Add listener to dialog to accept deletion
    await this.dialogListener(page, true);
    // Click on dropDown
    await Promise.all([
      page.click(this.zonesGridColumnToggleDropdown(row)),
      this.waitForVisibleSelector(page, `${this.zonesGridColumnToggleDropdown(row)}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.zonesGridDeleteLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await page.click(this.confirmDeleteButton);
    await this.elementNotVisible(page, this.confirmDeleteModal, 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all rows column content
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

  /* Bulk actions methods */

  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async bulkSelectRows(page: Page): Promise<void> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllLabel, (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
  }

  /**
   * Bulk delete
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteZones(page: Page): Promise<string> {
    await this.bulkSelectRows(page);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteSelectionButton),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await page.click(this.confirmDeleteButton);
    await this.elementNotVisible(page, this.confirmDeleteModal, 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Bulk set status
   * @param page {Page} Browser tab
   * @param wantedStatus {boolean} True if we need to bulk enable status, false if not
   * @return {Promise<string>}
   */
  async bulkSetStatus(page: Page, wantedStatus: boolean): Promise<string> {
    // Select all rows
    await this.bulkSelectRows(page);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click to change status
    await page.click(wantedStatus ? this.enableSelectionButton : this.disableSelectionButton);
    await this.elementNotVisible(page, wantedStatus ? this.enableSelectionButton : this.disableSelectionButton, 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Number of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

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
}

export default new Zones();
