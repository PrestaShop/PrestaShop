require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Tags page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Tags extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on tags page
   */
  constructor() {
    super();

    this.pageTitle = 'Tags •';

    // Selectors
    // Header links
    this.addNewTagLink = '#page-header-desc-tag-new_tag';

    // Form selectors
    this.gridForm = '#form-tag';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-tag';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='tagFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtontag';
    this.filterResetButton = 'button[name=\'submitResettag\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnLanguage = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnProducts = row => `${this.tableBodyColumn(row)}:nth-child(5)`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = number => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_tag';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
  }

  /*
  Methods
   */

  /* Header methods */
  /**
   * Go to add new tag page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewTagPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewTagLink);
  }

  /* Filter methods */
  /**
   * Get Number of lines
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
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Reset and get number of lines
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter Table
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter with
   * @param value {string} value to filter with
   * @return {Promise<void>}
   */
  async filterTable(page, filterBy, value = '') {
    await this.setValue(page, this.filterColumn(filterBy), value);
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /* Column methods */
  /**
   * Go to edit page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async gotoEditTagPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name to get
   * @return {Promise<string>}
   */
  async getTextColumn(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'id_tag':
        columnSelector = this.tableColumnId(row);
        break;

      case 'l!name':
        columnSelector = this.tableColumnLanguage(row);
        break;

      case 'a!name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'products':
        columnSelector = this.tableColumnProducts(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Delete tag from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteTag(page, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockContent(page);
  }

  // Sort methods
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


  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column name to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    let columnSelector;

    switch (sortBy) {
      case 'id_tag':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'l!name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'a!name':
        columnSelector = this.sortColumnDiv(4);
        break;

      case 'products':
        columnSelector = this.sortColumnDiv(5);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForNavigation(page, sortColumnButton);
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
   * @param number {number} Pagination limit number to select
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

  /* Bulk actions methods */
  /**
   * Bulk delete tags
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDelete(page) {
    // To confirm bulk delete action with dialog
    this.dialogListener(page, true);

    // Select all rows
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.selectAllLink),
    ]);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);

    // Perform delete
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new Tags();
