require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class WebService extends BOBasePage {
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
    this.webserviceListTableColumnAction = row => this.webserviceListTableColumn(row, 'actions');
    this.webserviceListTableToggleDropDown = row => `${this.webserviceListTableColumnAction(row)
    } a[data-toggle='dropdown']`;
    this.webserviceListTableDeleteLink = row => `${this.webserviceListTableColumnAction(row)} a.grid-delete-row-link`;
    this.webserviceListTableEditLink = row => `${this.webserviceListTableColumnAction(row)} a.grid-edit-row-link`;
    this.webserviceListColumnValidIcon = row => `${this.webserviceListTableColumn(row, 'active')
    } i.grid-toggler-icon-valid`;
    this.webserviceListColumnNotValidIcon = row => `${this.webserviceListTableColumn(row, 'active')
    } i.grid-toggler-icon-not-valid`;

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
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewWebserviceKeyPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewWebserviceLink);
  }

  /**
   * Get number of elements in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.webserviceGridTitle);
  }

  /**
   * Reset input filters
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * get text from a column from table
   * @param page
   * @param row
   * @param column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page, row, column) {
    return this.getTextContent(page, this.webserviceListTableColumn(row, column));
  }

  /**
   * Go to edit webservice key page
   * @param page
   * @param row, row in table
   * @returns {Promise<void>}
   */
  async goToEditWebservicePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.webserviceListTableEditLink(row));
  }

  /**
   * Filter list of webservice
   * @param page
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @returns {Promise<void>}
   */
  async filterWebserviceTable(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.webserviceFilterInput(filterBy), value.toString());
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
   * Get Value of column displayed
   * @param page
   * @param row, row in table
   * @returns {Promise<boolean>}
   */
  async getStatus(page, row) {
    return this.elementVisible(page, this.webserviceListColumnValidIcon(row), 100);
  }

  /**
   * Quick edit toggle column value
   * @param page
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @returns {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page, row, valueWanted = true) {
    await this.waitForVisibleSelector(page, this.webserviceListTableColumn(row, 'active'), 2000);
    if (await this.getStatus(page, row) !== valueWanted) {
      await page.click(this.webserviceListTableColumn(row, 'active'));
      await this.waitForVisibleSelector(
        page,
        (valueWanted ? this.webserviceListColumnValidIcon(row) : this.webserviceListColumnNotValidIcon(row)),
      );
      return true;
    }
    return false;
  }

  /**
   * Delete webservice key
   * @param page
   * @param row, row in table
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
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete with in modal
   * @param page
   * @return {Promise<void>}
   */
  async confirmDeleteWebService(page) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
  }

  /**
   * Get validation message
   * @param page
   * @returns {Promise<string>}
   */
  getValidationMessage(page) {
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all sql queries with Bulk Actions
   * @param page
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
    await this.confirmDeleteWebService(page, this.modalDeleteButton);

    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Enable / disable by Bulk Actions
   * @param page
   * @param enable
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
   * @param page
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTable(page, i, column);
      await allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table by clicking on column name
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
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
   * @param page
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
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

module.exports = new WebService();
