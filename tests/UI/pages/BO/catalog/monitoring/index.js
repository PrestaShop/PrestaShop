require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Monitoring extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Monitoring •';

    // Selectors
    this.gridPanel = table => `#${table}_grid_panel`;
    this.gridTable = table => `#${table}_grid_table`;
    this.gridHeaderTitle = table => `${this.gridPanel(table)} div.card-header h3`;
    // Filters
    this.filterColumn = (table, filterBY) => `${this.gridTable(table)} #${table}_${filterBY}`;
    this.filterSearchButton = table => `${this.gridTable(table)} button[name='${table}[actions][search]']`;
    this.filterResetButton = table => `${this.gridTable(table)} button[name='${table}[actions][reset]']`;
    // Table
    this.tableBody = table => `${this.gridTable(table)} tbody`;
    this.tableRow = (table, row) => `${this.tableBody(table)} tr:nth-child(${row})`;
    this.tableEmptyRow = table => `${this.tableBody(table)} tr.empty_row`;
    this.tableColumn = (table, row, column) => `${this.tableRow(table, row)} td.column-${column}`;
    // enable column
    this.enableColumn = (table, row) => this.tableColumn(table, row, 'active');
    this.enableColumnValidIcon = row => `${this.enableColumn(row)} i.grid-toggler-icon-valid`;
    // Actions buttons in Row
    this.actionsColumn = (table, row) => `${this.tableRow(table, row)} td.column-actions`;
    this.dropdownToggleButton = (table, row) => `${this.actionsColumn(table, row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (table, row) => `${this.actionsColumn(table, row)} div.dropdown-menu`;
    this.deleteRowLink = (table, row) => `${this.dropdownToggleMenu(table, row)} a[href*='/delete']`;
    this.deleteCategoryRowLink = row => `${this.dropdownToggleMenu('empty_category', row)
    } a.js-delete-category-row-action`;
    this.deleteModeModal = '#empty_category_grid_delete_categories_modal';
    this.deleteModeInput = position => `#delete_categories_delete_mode_${position}`;
    this.submitDeleteModeButton = `${this.deleteModeModal} button.js-submit-delete-categories`;
    // Sort Selectors
    this.tableHead = table => `${this.gridTable(table)} thead`;
    this.sortColumnDiv = (table, column) => `${this.tableHead(table)
    } div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (table, column) => `${this.sortColumnDiv(table, column)} span.ps-sort`;
  }

  /* Reset Methods */
  /**
   * Get number of element in table grid
   * @param page
   * @param table, which table to get number of element from
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page, table) {
    return this.getNumberFromText(page, this.gridHeaderTitle(table));
  }

  /**
   * Reset filters in table
   * @param page
   * @param table, which table to reset
   * @return {Promise<void>}
   */
  async resetFilter(page, table) {
    if (!(await this.elementNotVisible(page, this.filterResetButton(table), 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton(table));
    }
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @param table, which table to reset
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page, table) {
    await this.resetFilter(page, table);
    return this.getNumberOfElementInGrid(page, table);
  }


  /* Filter Methods */
  /**
   * Filter Table
   * @param page
   * @param table, which table to filter
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(page, table, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(table, filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(table, filterBy), value ? 'Yes' : 'No');
        break;
      default:
        throw new Error(`Filter column not found : ${filterBy}`);
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton(table));
  }

  /* table methods */
  /**
   * get text from a column
   * @param page
   * @param table, which table to get text from
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page, table, row, column) {
    return this.getTextContent(page, this.tableColumn(table, row, column));
  }

  /**
   * Open dropdown menu in table
   * @param page
   * @param table
   * @param row
   * @return {Promise<void>}
   */
  async openDropdownMenu(page, table, row) {
    await Promise.all([
      page.click(this.dropdownToggleButton(table, row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(table, row)}[aria-expanded='true']`),
    ]);
  }

  /**
   * Delete Row in table
   * @param page
   * @param table
   * @param row, row to delete
   * @returns {Promise<string>}
   */
  async deleteProductInGrid(page, table, row) {
    this.dialogListener(page, true);
    await this.openDropdownMenu(page, table, row);
    await this.clickAndWaitForNavigation(page, this.deleteRowLink(table, row));
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Categories methods */
  /**
   * Delete Row in table empty categories
   * @param page
   * @param table
   * @param row, row to delete
   * @param deletionModePosition, which mode to choose for delete
   * @returns {Promise<string>}
   */
  async deleteCategoryInGrid(page, table, row, deletionModePosition) {
    this.dialogListener(page, true);
    await this.openDropdownMenu(page, table, row);
    await Promise.all([
      page.click(this.deleteCategoryRowLink(row)),
      this.waitForVisibleSelector(page, this.deleteModeModal),
    ]);
    // choose deletion mode
    await page.click(this.deleteModeInput(deletionModePosition));
    await this.clickAndWaitForNavigation(page, this.submitDeleteModeButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get status
   * @param page
   * @param table
   * @param row
   * @returns {Promise<boolean>}
   */
  async getStatus(page, table, row = 1) {
    return this.elementVisible(page, this.enableColumnValidIcon(table, row), 100);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page
   * @param table
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, table, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page, table);
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      let rowContent = await this.getTextContent(page, this.tableColumn(table, i, column));
      if (column === 'active') {
        rowContent = await this.getStatus(page, table, i).toString();
      }
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param page
   * @param table
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, table, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(table, sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(table, sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }
}

module.exports = new Monitoring();
