require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Suppliers extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Suppliers • ';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors header
    this.newSupplierLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#supplier_grid_panel';
    this.gridTable = '#supplier_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsEnableButton = `${this.gridPanel} #supplier_grid_bulk_action_suppliers_enable_selection`;
    this.bulkActionsDisableButton = `${this.gridPanel} #supplier_grid_bulk_action_suppliers_disable_selection`;
    this.bulkActionsDeleteButton = `${this.gridPanel} #supplier_grid_bulk_action_delete_selection`;
    this.confirmDeleteModal = '#supplier_grid_confirm_modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
    // Filters
    this.filterColumn = filterBy => `${this.gridTable} #supplier_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} button[name='supplier[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='supplier[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    // Actions buttons in Row
    this.actionsColumn = row => `${this.tableRow(row)} td.column-actions`;
    this.viewRowLink = row => `${this.actionsColumn(row)} a[data-original-title='View']`;
    this.dropdownToggleButton = row => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = row => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.editRowLink = row => `${this.dropdownToggleMenu(row)} a[href*='/edit']`;
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
   * @return {Promise<void>}
   */
  async goToAddNewSupplierPage() {
    await this.clickAndWaitForNavigation(this.newSupplierLink);
  }

  /* Column Methods */
  /**
   * View Supplier
   * @param row, Which row of the list
   * @return {Promise<void>}
   */
  async viewSupplier(row = 1) {
    await this.clickAndWaitForNavigation(this.viewRowLink(row));
  }

  /**
   * Edit Supplier
   * @param row
   * @return {Promise<void>}
   */
  async goToEditSupplierPage(row = 1) {
    await Promise.all([
      this.page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(`${this.dropdownToggleButton(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(this.editRowLink(row));
  }

  /**
   * Delete Row in table
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteSupplier(row = 1) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(`${this.dropdownToggleButton(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink(row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Get toggle column value for a row
   * @param row
   * @return {Promise<string>}
   */
  async getToggleColumnValue(row = 1) {
    return this.elementVisible(this.enableColumnValidIcon(row), 100);
  }

  /**
   * Update Enable column for the value wanted in Brands list
   * @param row
   * @param valueWanted
   * @return {Promise<boolean>}, true if click has been performed
   */
  async updateEnabledValue(row = 1, valueWanted = true) {
    await this.waitForVisibleSelector(this.enableColumn(row), 2000);
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      await this.clickAndWaitForNavigation(this.enableColumn(row));
      return true;
    }
    return false;
  }

  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableSupplier(row, column) {
    return this.getTextContent(this.tableColumn(row, column));
  }

  /* Reset Methods */
  /**
   * Reset filters in table
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
  }

  /* filter Methods */
  /**
   * Filter Table
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.filterColumn(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(this.filterColumn(filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Filter Supplier column active
   * @param value
   * @return {Promise<void>}
   */
  async filterSupplierEnabled(value) {
    await this.filterTable('select', 'active', value ? 'Yes' : 'No');
  }

  /* Bulk Actions Methods */
  /**
   * Enable / disable Suppliers by Bulk Actions
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeSuppliersEnabledColumnBulkActions(enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete with bulk actions
   * @return {Promise<textContent>}
   */
  async deleteWithBulkActions() {
    // Click on Select All
    await Promise.all([
      this.page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(`${this.confirmDeleteModal}.show`),
    ]);
    await this.clickAndWaitForNavigation(this.confirmDeleteButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Get alert text message
   * @returns {Promise<string>|*}
   */
  getAlertTextMessage() {
    return this.getTextContent(this.alertTextBlock);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(column) {
    const rowsNumber = await this.getNumberOfElementInGrid();
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      let rowContent = await this.getTextContent(this.tableColumn(i, column));
      if (column === 'active') {
        rowContent = await this.getToggleColumnValue(i).toString();
      }
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @return {Promise<string>}
   */
  getPaginationLabel() {
    return this.getTextContent(this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param number
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(number) {
    await this.selectByVisibleText(this.paginationLimitSelect, number);
    return this.getPaginationLabel();
  }

  /**
   * Click on next
   * @returns {Promise<string>}
   */
  async paginationNext() {
    await this.clickAndWaitForNavigation(this.paginationNextLink);
    return this.getPaginationLabel();
  }

  /**
   * Click on previous
   * @returns {Promise<string>}
   */
  async paginationPrevious() {
    await this.clickAndWaitForNavigation(this.paginationPreviousLink);
    return this.getPaginationLabel();
  }
};
