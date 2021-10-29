require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Webservice page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class WebService extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on webservice page
   */
  constructor() {
    super();

    this.pageTitle = 'Webservice •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    // Header links
    this.addNewWebserviceLink = '#page-header-desc-configuration-add[title=\'Add new webservice key\']';

    // List of webservices
    this.webserviceGridPanel = '#webservice_key_grid_panel';
    this.webserviceGridTitle = `${this.webserviceGridPanel} h3.card-header-title`;
    this.webserviceListForm = '#webservice_key_grid';
    this.webserviceListTableRow = row => `${this.webserviceListForm} tbody tr:nth-child(${row})`;
    this.webserviceListTableColumn = (row, column) => `${this.webserviceListTableRow(row)} td.column-${column}`;
    this.webserviceListTableStatusColumn = row => `${this.webserviceListTableColumn(row, 'active')} .ps-switch`;
    this.webserviceListTableStatusColumnToggleInput = row => `${this.webserviceListTableStatusColumn(row)} input`;
    this.webserviceListTableColumnAction = row => this.webserviceListTableColumn(row, 'actions');
    this.webserviceListTableToggleDropDown = row => `${this.webserviceListTableColumnAction(row)
    } a[data-toggle='dropdown']`;
    this.webserviceListTableDeleteLink = row => `${this.webserviceListTableColumnAction(row)} a.grid-delete-row-link`;
    this.webserviceListTableEditLink = row => `${this.webserviceListTableColumnAction(row)} a.grid-edit-row-link`;

    // Filters
    this.webserviceFilterInput = filterBy => `${this.webserviceListForm} #webservice_key_${filterBy}`;
    this.filterSearchButton = `${this.webserviceListForm} .grid-search-button`;
    this.filterResetButton = `${this.webserviceListForm} .grid-reset-button`;

    // Delete modal
    this.confirmDeleteModal = '#webservice_key-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Sort Selectors
    this.tableHead = `${this.webserviceListForm} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Bulk Actions
    this.selectAllRowsDiv = `${this.webserviceListForm} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.webserviceListForm} button.dropdown-toggle`;
    this.bulkActionsDeleteButton = `${this.webserviceListForm} #webservice_key_grid_bulk_action_delete_selection`;
    this.bulkActionsEnableButton = `${this.webserviceListForm}
    #webservice_key_grid_bulk_action_webservice_enable_selection`;
    this.bulkActionsDisableButton = `${this.webserviceListForm}
    #webservice_key_grid_bulk_action_webservice_disable_selection`;

    // Modal Dialog
    this.deleteModal = '#webservice_key-grid-confirm-modal.show';
    this.modalDeleteButton = `${this.deleteModal} button.btn-confirm-submit`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.webserviceGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.webserviceGridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.webserviceGridPanel} [aria-label='Previous']`;
  }

  /*
  Methods
   */

  /**
   * Go to new webservice key page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewWebserviceKeyPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewWebserviceLink);
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.webserviceGridTitle);
  }

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Get text from a column from table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page, row, column) {
    return this.getTextContent(page, this.webserviceListTableColumn(row, column));
  }

  /**
   * Go to edit webservice key page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditWebservicePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.webserviceListTableEditLink(row));
  }

  /**
   * Filter list of webservice
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to put on filter
   * @returns {Promise<void>}
   */
  async filterWebserviceTable(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.webserviceFilterInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.webserviceFilterInput(filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get value of column displayed
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<boolean>}
   */
  async getStatus(page, row) {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.webserviceListTableStatusColumnToggleInput(row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we want to enable status, false if not
   * @returns {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page, row, valueWanted = true) {
    if (await this.getStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForNavigation(page, this.webserviceListTableStatusColumn(row));
      return true;
    }

    return false;
  }

  /**
   * Delete webservice key
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteWebserviceKey(page, row) {
    // Click on dropDown
    await Promise.all([
      page.click(this.webserviceListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.webserviceListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await Promise.all([
      page.click(this.webserviceListTableDeleteLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteWebService(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteWebService(page) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
  }

  /**
   * Get validation message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getValidationMessage(page) {
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all sql queries with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteWithBulkActions(page) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsDiv, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(page, this.deleteModal),
    ]);
    await this.confirmDeleteWebService(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable / disable by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to bulk enable status, false if not
   * @returns {Promise<void>}
   */
  async bulkSetStatus(page, enable = true) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsDiv, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);

    // Click on enable/Disable and wait for modal
    await this.clickAndWaitForNavigation(page, enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to get text value
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTable(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} column to sort with
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
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForNavigation({waitUntil: 'networkidle'}),
    ]);

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

module.exports = new WebService();
