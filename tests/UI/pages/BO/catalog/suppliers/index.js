require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Suppliers extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Suppliers • ';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors header
    this.newSupplierLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#supplier_grid_panel';
    this.gridTable = '#supplier_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsEnableButton = `${this.gridPanel} #supplier_grid_bulk_action_suppliers_enable_selection`;
    this.bulkActionsDisableButton = `${this.gridPanel} #supplier_grid_bulk_action_suppliers_disable_selection`;
    this.bulkActionsDeleteButton = `${this.gridPanel} #supplier_grid_bulk_action_delete_selection`;
    this.confirmDeleteModal = '#supplier-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
    // Filters
    this.filterColumn = filterBy => `${this.gridTable} #supplier_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    // Actions buttons in Row
    this.actionsColumn = row => `${this.tableRow(row)} td.column-actions`;
    this.viewRowLink = row => `${this.actionsColumn(row)} a.grid-view-row-link`;
    this.dropdownToggleButton = row => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = row => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.editRowLink = row => `${this.dropdownToggleMenu(row)} a.grid-edit-row-link`;
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a[data-url*='/delete']`;
    // enable column
    this.enableColumn = row => this.tableColumn(row, 'active');
    this.enableColumnValidIcon = row => `${this.enableColumn(row)} i.grid-toggler-icon-valid`;
    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.gridPanel} [aria-label='Previous']`;
  }

  /*
  Methods
   */

  /* Header Methods */
  /**
   * Go to New Supplier Page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewSupplierPage(page) {
    await this.clickAndWaitForNavigation(page, this.newSupplierLink);
  }

  /* Column Methods */
  /**
   * View Supplier
   * @param page
   * @param row, Which row of the list
   * @return {Promise<void>}
   */
  async viewSupplier(page, row = 1) {
    await this.clickAndWaitForNavigation(page, this.viewRowLink(row));
  }

  /**
   * Edit Supplier
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async goToEditSupplierPage(page, row = 1) {
    await Promise.all([
      page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(page, this.editRowLink(row));
  }

  /**
   * Delete Row in table
   * @param page
   * @param row, row to delete
   * @return {Promise<string>}
   */
  async deleteSupplier(page, row = 1) {
    await Promise.all([
      page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(row)}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.deleteRowLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteSuppliers(page);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Get toggle column value for a row
   * @param page
   * @param row
   * @return {Promise<boolean>}
   */
  async getStatus(page, row = 1) {
    return this.elementVisible(page, this.enableColumnValidIcon(row), 100);
  }

  /**
   * Update Enable column for the value wanted in Brands list
   * @param page
   * @param row
   * @param valueWanted
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setStatus(page, row = 1, valueWanted = true) {
    await this.waitForVisibleSelector(page, this.enableColumn(row), 2000);
    if (await this.getStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForNavigation(page, this.enableColumn(row));
      return true;
    }

    return false;
  }

  /**
   * get text from a column
   * @param page
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  async getTextColumnFromTableSupplier(page, row, column) {
    return this.getTextContent(page, this.tableColumn(row, column));
  }

  /* Reset Methods */
  /**
   * Reset filters in table
   * @param page
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /* filter Methods */
  /**
   * Filter Table
   * @param page
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Filter Supplier column active
   * @param page
   * @param value
   * @return {Promise<void>}
   */
  async filterSupplierEnabled(page, value) {
    await this.filterTable(page, 'select', 'active', value ? 'Yes' : 'No');
  }

  /* Bulk Actions Methods */
  /**
   * Enable / disable Suppliers by Bulk Actions
   * @param page
   * @param enable
   * @return {Promise<string>}
   */
  async bulkSetStatus(page, enable = true) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(page, enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Delete with bulk actions
   * @param page
   * @return {Promise<string>}
   */
  async deleteWithBulkActions(page) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, el => el.click()),
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
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteSuppliers(page);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete with modal
   * @param page
   * @return {Promise<void>}
   */
  async confirmDeleteSuppliers(page) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
  }

  /**
   * Get alert text message
   * @returns {Promise<string>}
   */
  getAlertTextMessage(page) {
    return this.getTextContent(page, this.alertTextBlock);
  }

  // Sort methods
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
      let rowContent = await this.getTextContent(page, this.tableColumn(i, column));
      if (column === 'active') {
        rowContent = await this.getStatus(page, i).toString();
      }
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection = 'asc') {
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
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
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

module.exports = new Suppliers();
