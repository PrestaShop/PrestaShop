require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Pages page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Pages extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on pages page
   */
  constructor() {
    super();

    this.pageTitle = 'Pages';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Header link
    this.addNewPageCategoryLink = '#page-header-desc-configuration-add_cms_category[title=\'Add new page category\']';
    this.addNewPageLink = '#page-header-desc-configuration-add_cms_page[title=\'Add new page\']';

    // Common Selectors
    this.gridPanel = table => `#${table}_grid_panel`;
    this.gridTitle = table => `${this.gridPanel(table)} h3.card-header-title`;
    this.gridTable = table => `#${table}_grid_table`;
    this.gridHeaderTitle = table => `${this.gridPanel(table)} h3.card-header-title`;
    this.listForm = table => `#${table}_grid`;

    // Sort Selectors
    this.tableHead = table => `${this.listForm(table)} thead`;
    this.sortColumnDiv = (table, column) => `${this.tableHead(table)}`
      + ` div.ps-sortable-column[data-sort-col-name='${column}']`;

    this.sortColumnSpanButton = (table, column) => `${this.sortColumnDiv(table, column)} span.ps-sort`;
    this.listTableRow = (table, row) => `${this.listForm(table)} tbody tr:nth-child(${row})`;
    this.listTableColumn = (table, row, column) => `${this.listTableRow(table, row)} td.column-${column}`;
    this.listTableStatusColumn = (table, row) => `${this.listTableColumn(table, row, 'active')} .ps-switch`;
    this.listTableStatusColumnToggleInput = (table, row) => `${this.listTableStatusColumn(table, row)} input`;

    // Bulk Actions
    this.selectAllRowsLabel = table => `${this.listForm(table)} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = table => `${this.listForm(table)} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = table => `#${table}_grid_bulk_action_delete_selection`;
    this.bulkActionsEnableButton = table => `#${table}_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = table => `#${table}_grid_bulk_action_disable_selection`;
    this.confirmDeleteModal = table => `#${table}-grid-confirm-modal`;
    this.confirmDeleteButton = table => `${this.confirmDeleteModal(table)} button.btn-confirm-submit`;

    // Filters
    this.filterColumn = (table, filterBy) => `${this.gridTable(table)} #${table}_${filterBy}`;
    this.filterSearchButton = table => `${this.gridTable(table)} .grid-search-button`;
    this.filterResetButton = table => `${this.gridTable(table)} .grid-reset-button`;

    // Actions buttons in Row
    this.listTableToggleDropDown = (table, row) => `${this.listTableColumn(table, row, 'actions')}`
      + ' a[data-toggle=\'dropdown\']';

    this.listTableEditLink = (table, row) => `${this.listTableColumn(table, row, 'actions')}`
      + ' a.grid-edit-row-link';

    this.deleteRowLink = (table, row) => `${this.listTableColumn(table, row, 'actions')} a.grid-delete-row-link`;

    // Categories selectors
    this.backToListButton = '#cms_page_category_grid_panel a.back-to-list-link';
    this.categoriesListTableViewLink = row => `${this.listTableColumn('cms_page_category', row, 'actions')}`
      + ' a.grid-view-row-link';

    this.categoriesPaginationLimitSelect = '#paginator_select_page_limit';
    this.categoriesPaginationLabel = `${this.listForm('cms_page_category')} .col-form-label`;
    this.categoriesPaginationNextLink = `${this.listForm('cms_page_category')} #pagination_next_url`;
    this.categoriesPaginationPreviousLink = `${this.listForm('cms_page_category')} [aria-label='Previous']`;

    // Pages selectors
    this.pagesPaginationLimitSelect = '#paginator_select_page_limit';
    this.pagesPaginationLabel = `${this.listForm('cms_page')} .col-form-label`;
    this.pagesPaginationNextLink = `${this.listForm('cms_page')} #pagination_next_url`;
    this.pagesPaginationPreviousLink = `${this.listForm('cms_page')} [aria-label='Previous']`;
  }

  /*
 Methods
  */

  // Common methods

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset and get number of lines
   * @return {Promise<void>}
   */
  async resetAndGetNumberOfLines(page, tableName) {
    const resetButton = this.filterResetButton(tableName);

    if (await this.elementVisible(page, resetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, resetButton);
    }
    return this.getNumberFromText(page, this.gridHeaderTitle(tableName));
  }

  /**
   * Filter table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to filter
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterTable(page, tableName, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(tableName, filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(tableName, filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton(tableName));
  }

  /**
   * Delete row in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete row from it
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteRowInTable(page, tableName, row) {
    // Click on dropDown
    await Promise.all([
      page.click(this.listTableToggleDropDown(tableName, row)),
      this.waitForVisibleSelector(page, `${this.listTableToggleDropDown(tableName, row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteRowLink(tableName, row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal(tableName)}.show`),
    ]);
    await this.confirmDeleteFromTable(page, tableName);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all rows in table with Bulk Actions
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete rows with bulk actions
   * @returns {Promise<string>}
   */
  async deleteWithBulkActions(page, tableName) {
    // Add listener to dialog to accept deletion
    this.dialogListener(page);

    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel(tableName), el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton(tableName)),
      this.waitForVisibleSelector(page, this.bulkActionsDeleteButton(tableName)),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.bulkActionsDeleteButton(tableName)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal(tableName)}.show`),
    ]);
    await this.confirmDeleteFromTable(page, tableName);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with in modal
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to confirm delete
   * @return {Promise<void>}
   */
  async confirmDeleteFromTable(page, tableName) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton(tableName));
  }

  /**
   * Get value of column Displayed in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get status
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page, tableName, row) {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.listTableStatusColumnToggleInput(tableName, row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Quick edit toggle column value in table
   * @param page {Page} Browser tab
   * @param tableName {string} table name to set status
   * @param row {number} row on table
   * @param valueWanted {boolean} Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page, tableName, row, valueWanted = true) {
    if (await this.getStatus(page, tableName, row) !== valueWanted) {
      await this.clickAndWaitForNavigation(page, this.listTableStatusColumn(tableName, row));

      return true;
    }

    return false;
  }

  /**
   * Enable / disable column by Bulk Actions
   * @param page {Page} Browser tab
   * @param tableName {string}  Table name to bulk delete rows
   * @param enable {boolean} True if we need to bulk enable, false if not
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page, tableName, enable = true) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel(tableName), el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton(tableName)),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}`),
    ]);

    // Click on enable/disable and wait for modal
    await this.clickAndWaitForNavigation(
      page,
      enable ? this.bulkActionsEnableButton(tableName) : this.bulkActionsDisableButton(tableName),
    );

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * get text from a column
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get text column
   * @param row {number} Row on table
   * @param column{string} Column name to get text from it
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(page, tableName, row, column) {
    return this.getTextContent(page, this.listTableColumn(tableName, row, column));
  }

  /**
   * Get text column form table cms page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column name to get text from it
   * @return {Promise<string>}
   */
  getTextColumnFromTableCmsPage(page, row, column) {
    return this.getTextColumnFromTable(page, 'cms_page', row, column);
  }

  /**
   * Get text column form table cms page category
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column name to get text from it
   * @return {Promise<string>}
   */
  getTextColumnFromTableCmsPageCategory(page, row, column) {
    return this.getTextColumnFromTable(page, 'cms_page_category', row, column);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get all rows column content
   * @param column {string} Column name to get all rows column content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, tableName, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page, tableName);
    const allRowsContentTable = [];
    let rowContent;

    for (let i = 1; i <= rowsNumber; i++) {
      if (tableName === 'cms_page_category') {
        rowContent = await this.getTextColumnFromTableCmsPageCategory(page, i, column);
      } else if (tableName === 'cms_page') {
        rowContent = await this.getTextColumnFromTableCmsPage(page, i, column);
      }
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Get content from all rows table cms page category
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows column content
   * @return {Promise<Array<string>>}
   */
  getAllRowsColumnContentTableCmsPageCategory(page, column) {
    return this.getAllRowsColumnContent(page, 'cms_page_category', column);
  }

  /**
   * Get content from all rows table cms page
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows column content
   * @returns {Promise<Array<string>>}
   */
  getAllRowsColumnContentTableCmsPage(page, column) {
    return this.getAllRowsColumnContent(page, 'cms_page', column);
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get number of elements
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page, tableName) {
    return this.getNumberFromText(page, this.gridTitle(tableName));
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to sort
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, tableName, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(tableName, sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(tableName, sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Sort table cms page category
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTableCmsPageCategory(page, sortBy, sortDirection = 'asc') {
    return this.sortTable(page, 'cms_page_category', sortBy, sortDirection);
  }

  /**
   * Sort table cms page
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTableCmsPage(page, sortBy, sortDirection = 'asc') {
    return this.sortTable(page, 'cms_page', sortBy, sortDirection);
  }

  // Category methods

  /**
   * Go to Edit Category page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditCategoryPage(page, row) {
    // Click on dropDown
    await Promise.all([
      page.click(this.listTableToggleDropDown('cms_page_category', row)),
      this.waitForVisibleSelector(
        page,
        `${this.listTableToggleDropDown('cms_page_category', row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on edit
    await this.clickAndWaitForNavigation(page, this.listTableEditLink('cms_page_category', row));
  }

  /**
   * Go to new Page Category page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewPageCategory(page) {
    await this.clickAndWaitForNavigation(page, this.addNewPageCategoryLink);
  }

  /**
   * Back to Pages page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async backToList(page) {
    await this.clickAndWaitForNavigation(page, this.backToListButton);
  }

  /**
   * View Category
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async viewCategory(page, row) {
    await this.clickAndWaitForNavigation(page, this.categoriesListTableViewLink(row));
  }

  // Page methods

  /**
   * Go to new Page page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewPageLink);
  }

  /**
   * Go to Edit page Page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.listTableEditLink('cms_page', row));
  }

  /**
   * Select category pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectCategoryPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.categoriesPaginationLimitSelect, number);

    return this.getTextContent(page, this.categoriesPaginationLabel);
  }

  /**
   * Category pagination next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationCategoryNext(page) {
    await this.clickAndWaitForNavigation(page, this.categoriesPaginationNextLink);

    return this.getTextContent(page, this.categoriesPaginationLabel);
  }

  /**
   * Category pagination previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationCategoryPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.categoriesPaginationPreviousLink);

    return this.getTextContent(page, this.categoriesPaginationLabel);
  }

  /**
   * Select pages pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit number
   * @returns {Promise<string>}
   */
  async selectPagesPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.pagesPaginationLimitSelect, number);

    return this.getTextContent(page, this.pagesPaginationLabel);
  }

  /**
   * Pages pagination next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPagesNext(page) {
    await this.clickAndWaitForNavigation(page, this.pagesPaginationNextLink);

    return this.getTextContent(page, this.pagesPaginationLabel);
  }

  /**
   * Pages pagination previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPagesPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.pagesPaginationPreviousLink);

    return this.getTextContent(page, this.pagesPaginationLabel);
  }
}

module.exports = new Pages();
