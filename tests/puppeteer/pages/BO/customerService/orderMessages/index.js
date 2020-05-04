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
    this.confirmDeleteModal = '#order_message_grid_confirm_modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
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
};
