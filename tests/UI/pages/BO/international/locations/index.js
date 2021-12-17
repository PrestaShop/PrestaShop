require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Zones page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Zones extends BOBasePage {
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
    this.zonesFilterColumnInput = filterBy => `${this.zonesGridTable} #zone_${filterBy}`;
    this.resetFilterButton = `${this.zonesGridTable} .grid-reset-button`;
    this.searchFilterButton = `${this.zonesGridTable} .grid-search-button`;
    this.zonesGridRow = row => `${this.zonesGridTable} tbody tr:nth-child(${row})`;
    this.zonesGridColumn = (row, column) => `${this.zonesGridRow(row)} td.column-${column}`;
    this.zonesGridStatusColumn = row => `${this.zonesGridColumn(row, 'active')} .ps-switch`;
    this.zonesGridStatusColumnToggleInput = row => `${this.zonesGridStatusColumn(row)} input`;
    this.zonesGridActionsColumn = row => this.zonesGridColumn(row, 'actions');
    this.zonesGridColumnEditLink = row => `${this.zonesGridActionsColumn(row)} a.grid-edit-row-link`;
    this.zonesGridColumnToggleDropdown = row => `${this.zonesGridActionsColumn(row)} a[data-toggle='dropdown']`;
    this.zonesGridDeleteLink = row => `${this.zonesGridActionsColumn(row)} a.grid-delete-row-link`;

    // Sort
    this.tableHead = `${this.zonesGridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.zonesGridPanelDiv} .col-form-label`;
    this.paginationNextLink = `${this.zonesGridPanelDiv} #pagination_next_url`;
    this.paginationPreviousLink = `${this.zonesGridPanelDiv} [aria-label='Previous']`;
  }

  /* Header methods */
  /**
   * Go to sub tab countries
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToSubTabCountries(page) {
    await this.clickAndWaitForNavigation(page, this.countriesSubTab);
  }

  /**
   * Go to sub tab states
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabStates(page) {
    await this.clickAndWaitForNavigation(page, this.statesSubTab);
  }

  /**
   * Go to add new zone page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewZonePage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewZoneLink);
  }

  /* Filter Methods */
  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.resetFilterButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.resetFilterButton);
    }
    await this.waitForVisibleSelector(page, this.searchFilterButton, 2000);
  }

  /**
   * Get number of zones
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset and get number of zones
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
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
  async filterZones(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.zonesFilterColumnInput(filterBy), value);
        break;

      case 'select':
        await this.selectByVisibleText(page, this.zonesFilterColumnInput(filterBy), value ? 'Yes' : 'No');
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.searchFilterButton);
  }

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name to get text column
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    return this.getTextContent(page, this.zonesGridColumn(row, columnName));
  }

  /**
   * Get zone status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getZoneStatus(page, row) {
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
  async setZoneStatus(page, row, wantedStatus) {
    if (wantedStatus !== await this.getZoneStatus(page, row)) {
      await this.clickAndWaitForNavigation(page, this.zonesGridStatusColumn(row));
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
  async goToEditZonePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.zonesGridColumnEditLink(row));
  }

  /**
   * Delete zone
   * @param page {Page} Browser tab
   * @param row {Number} Row on table
   * @return {Promise<string>}
   */
  async deleteZone(page, row) {
    // Add listener to dialog to accept deletion
    this.dialogListener(page, true);
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
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all rows column content
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
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
  }

  /**
   * Bulk delete
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteZones(page) {
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
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Bulk set status
   * @param page {Page} Browser tab
   * @param wantedStatus {boolean} True if we need to bulk enable status, false if not
   * @return {Promise<string>}
   */
  async bulkSetStatus(page, wantedStatus) {
    // Select all rows
    await this.bulkSelectRows(page);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click to change status
    await this.clickAndWaitForNavigation(page, wantedStatus ? this.enableSelectionButton : this.disableSelectionButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
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
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Number of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

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

module.exports = new Zones();
