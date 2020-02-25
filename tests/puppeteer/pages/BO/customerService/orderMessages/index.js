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
    this.filterColumn = `${this.gridTable} #order_message_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='order_message[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='order_message[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    // Actions buttons in Row
    this.actionsColumn = `${this.tableRow} td.column-actions`;
    this.editRowLink = `${this.actionsColumn} a[data-original-title='Edit']`;
    this.dropdownToggleButton = `${this.actionsColumn} a.dropdown-toggle`;
    this.dropdownToggleMenu = `${this.actionsColumn} div.dropdown-menu`;
    this.deleteRowLink = `${this.dropdownToggleMenu} a[data-url*='/delete']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} .md-checkbox label`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#order_message_grid_bulk_action_delete_selection';
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
    await this.setValue(this.filterColumn.replace('%FILTERBY', filterBy), value);
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /* Column Methods */
  /**
   * Edit order message
   * @param row
   * @return {Promise<void>}
   */
  async gotoEditOrderMessage(row = 1) {
    await this.clickAndWaitForNavigation(this.editRowLink.replace('%ROW', row));
  }

  /**
   * Delete Row in table
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteOrderMessage(row = 1) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`.replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink.replace('%ROW', row));
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTable(row, column) {
    return this.getTextContent(
      this.tableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
  }

  /* Bulk Actions Methods */
  /**
   * Delete with bulk actions
   * @return {Promise<textContent>}
   */
  async deleteWithBulkActions() {
    this.dialogListener(true);
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.page.waitForSelector(`${this.selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.page.waitForSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(this.bulkActionsDeleteButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
