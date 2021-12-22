require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Multistore page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class MultiStoreSettings extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on multistore page
   */
  constructor() {
    super();

    this.pageTitle = 'Multistore • ';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Header selectors
    this.newShopGroupLink = '#page-header-desc-shop_group-new';
    this.newShopLink = '#page-header-desc-shop_group-new_2';

    // Form selectors
    this.gridForm = '#form-shop_group';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-shop_group';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='shop_groupFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonshop_group';
    this.filterResetButton = 'button[name=\'submitResetshop_group\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;
    this.listTableColumn = (row, column) => `${this.tableBodyColumn(row)}:nth-child(${column})`;

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Multistore tree selectors
    this.multistoreTree = '#shops-tree';
    this.shopLink = id => `${this.multistoreTree} a[href*='shop_id=${id}']`;
    this.shopUrlLink = id => `${this.multistoreTree} a[href*='shop_url=${id}']`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = number => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} th:nth-child(${column})`;
  }

  /* Header methods */
  /**
   * Go to new shop group page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewShopGroupPage(page) {
    await this.clickAndWaitForNavigation(page, this.newShopGroupLink);
  }

  /**
   * Go to new shop page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewShopPage(page) {
    await this.clickAndWaitForNavigation(page, this.newShopLink);
  }

  /* Filter methods */

  /**
   * Get number of elements
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Reset and get number of shop groups
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter shop groups
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter
   * @param value {string} Value to put on filter
   * @return {Promise<void>}
   */
  async filterTable(page, filterBy, value) {
    await this.setValue(page, this.filterColumn(filterBy), value);
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Go to edit shop group page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async gotoEditShopGroupPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete shop group from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteShopGroup(page, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Is action toggle button visible
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<boolean>}
   */
  async isActionToggleButtonVisible(page, row) {
    return this.elementVisible(page, this.tableColumnActionsToggleButton(row));
  }

  /**
   * Go to shop link
   * @param page {Page} Browser tab
   * @param id {number} Row on table
   * @returns {Promise<void>}
   */
  async goToShopPage(page, id) {
    await this.clickAndWaitForNavigation(page, this.shopLink(id));
  }

  /**
   * Go to shop url link
   * @param page {Page} Browser tab
   * @param id {number} Row on table
   * @returns {Promise<void>}
   */
  async goToShopURLPage(page, id = 1) {
    await this.clickAndWaitForNavigation(page, this.shopUrlLink(id));
  }

  /**
   * Get text from a column from table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column name to get text content
   * @returns {Promise<string>}
   */
  async getTextColumn(page, row, column) {
    let columnSelector;

    switch (column) {
      case 'id_shop_group':
        columnSelector = this.listTableColumn(row, 1);
        break;

      case 'a!name':
        columnSelector = this.listTableColumn(row, 2);
        break;

      default:
        throw new Error(`Column ${column} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all text content
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

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.clickAndWaitForNavigation(page, this.paginationItems(number));

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

  /* Sort methods */
  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    let columnSelector;

    switch (sortBy) {
      case 'id_shop_group':
        columnSelector = this.sortColumnDiv(1);
        break;

      case 'a!name':
        columnSelector = this.sortColumnDiv(2);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }
    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForNavigation(page, sortColumnButton);
  }
}

module.exports = new MultiStoreSettings();
