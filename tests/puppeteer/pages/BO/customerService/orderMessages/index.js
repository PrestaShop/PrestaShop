require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class OrderMessages extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order Messages •';

    // Selectors header
    this.newOrderMessageLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#order_message_grid_panel';
    this.gridTable = '#order_message_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Filters
    this.filterColumn = filterBy => `${this.gridTable} #order_message_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} button[name='order_message[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='order_message[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    // Actions buttons in Row
    this.actionsColumn = row => `${this.tableRow(row)} td.column-actions`;
    this.editRowLink = row => `${this.actionsColumn(row)} a[data-original-title='Edit']`;
    this.dropdownToggleButton = row => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = row => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a[data-url*='/delete']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#order_message_grid_bulk_action_delete_selection';
    // Delete modal
    this.confirmDeleteModal = '#order_message-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.gridPanel} [aria-label='Previous']`;
    // Sort Selectors
    this.tableHead = `${this.gridPanel} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /* Header Methods */
  /**
   * Go to new order message page
   * @return {Promise<void>}
   */
  async goToAddNewOrderMessagePage() {
    await this.clickAndWaitForNavigation(this.newOrderMessageLink);
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
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(filterBy, value) {
    await this.setValue(this.filterColumn(filterBy), value);
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /* Column Methods */
  /**
   * Edit order message
   * @param row
   * @return {Promise<void>}
   */
  async gotoEditOrderMessage(row = 1) {
    await this.clickAndWaitForNavigation(this.editRowLink(row));
  }

  /**
   * Delete Row in table
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteOrderMessage(row = 1) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(
        `${this.dropdownToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink(row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTable(row, column) {
    return this.getTextContent(this.tableColumn(row, column));
  }

  /* Bulk Actions Methods */
  /**
   * Delete with bulk actions
   * @return {Promise<textContent>}
   */
  async deleteWithBulkActions() {
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.waitForVisibleSelector(`${this.selectAllRowsLabel}:not([disabled])`),
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
    // Click on delete and wait for modal
    await this.confirmDeleteOrderMessages();
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete in modal
   * @return {Promise<void>}
   */
  async confirmDeleteOrderMessages() {
    await this.clickAndWaitForNavigation(this.confirmDeleteButton);
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
   * @returns {Promise<string >}
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
};
