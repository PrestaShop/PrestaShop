require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Pages extends BOBasePage {
  constructor(page) {
    super(page);

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
    this.columnValidIcon = (table, row) => `${this.listTableColumn(table, row, 'active')}`
      + ' i.grid-toggler-icon-valid';
    this.columnNotValidIcon = (table, row) => `${this.listTableColumn(table, row, 'active')}`
      + ' i.grid-toggler-icon-not-valid';
    // Bulk Actions
    this.selectAllRowsLabel = table => `${this.listForm(table)} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = table => `${this.listForm(table)} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = table => `#${table}_grid_bulk_action_delete_selection`;
    this.bulkActionsEnableButton = table => `#${table}_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = table => `#${table}_grid_bulk_action_disable_selection`;
    this.confirmDeleteModal = table => `#${table}_grid_confirm_modal`;
    this.confirmDeleteButton = table => `${this.confirmDeleteModal(table)} button.btn-confirm-submit`;
    // Filters
    this.filterColumn = (table, filterBy) => `${this.gridTable(table)} #${table}_${filterBy}`;
    this.filterSearchButton = table => `${this.gridTable(table)} button[name='${table}[actions][search]']`;
    this.filterResetButton = table => `${this.gridTable(table)} button[name='${table}[actions][reset]']`;
    // Actions buttons in Row
    this.listTableToggleDropDown = (table, row) => `${this.listTableColumn(table, row, 'actions')}`
      + ' a[data-toggle=\'dropdown\']';
    this.listTableEditLink = (table, row) => `${this.listTableColumn(table, row, 'actions')} a[href*='edit']`;
    this.deleteRowLink = (table, row) => `${this.listTableColumn(table, row, 'actions')} a[data-method='DELETE']`;

    // Categories selectors
    this.backToListButton = '#cms_page_category_grid_panel div.card-footer a';
    this.categoriesListTableViewLink = row => `${this.listTableColumn('cms_page_category', row, 'actions')}`
      + ' a[data-original-title=\'View\']';
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
   * @param table
   * @return {Promise<void>}
   */
  async resetAndGetNumberOfLines(table) {
    const resetButton = this.filterResetButton(table);
    if (await this.elementVisible(resetButton, 2000)) {
      await this.clickAndWaitForNavigation(resetButton);
    }
    return this.getNumberFromText(this.gridHeaderTitle(table));
  }

  /**
   * Filter table
   * @param table
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterTable(table, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.filterColumn(table, filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.filterColumn(table, filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton(table));
  }

  /**
   * Delete row in table
   * @param table
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deleteRowInTable(table, row) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.listTableToggleDropDown(table, row)),
      this.waitForVisibleSelector(`${this.listTableToggleDropDown(table, row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    this.dialogListener();
    await this.clickAndWaitForNavigation(this.deleteRowLink(table, row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all rows in table with Bulk Actions
   * @param table
   * @return {Promise<textContent>}
   */
  async deleteWithBulkActions(table) {
    // Add listener to dialog to accept deletion
    this.dialogListener();
    // Click on Select All
    await Promise.all([
      this.page.$eval(this.selectAllRowsLabel(table), el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton(table)}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton(table)),
      this.waitForVisibleSelector(this.bulkActionsDeleteButton(table)),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.bulkActionsDeleteButton(table)),
      this.waitForVisibleSelector(`${this.confirmDeleteModal(table)}.show`),
    ]);
    await this.confirmDeleteWithBulkActions(table);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete with in modal
   * @param table
   * @return {Promise<void>}
   */
  async confirmDeleteWithBulkActions(table) {
    await this.clickAndWaitForNavigation(this.confirmDeleteButton(table));
  }

  /**
   * Get Value of column Displayed in table
   * @param table
   * @param row, row in table
   * @return {Promise<boolean>}
   */
  async getToggleColumnValue(table, row) {
    return this.elementVisible(this.columnValidIcon(table, row), 100);
  }

  /**
   * Quick edit toggle column value in table
   * @param table
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(table, row, valueWanted = true) {
    await this.waitForVisibleSelector(this.listTableColumn(table, row, 'active'), 2000);
    if (await this.getToggleColumnValue(table, row) !== valueWanted) {
      this.page.click(this.listTableColumn(table, row, 'active'));
      await this.waitForVisibleSelector(
        (valueWanted ? this.columnValidIcon : this.columnNotValidIcon)(table, row),
      );
      return true;
    }
    return false;
  }

  /**
   * Enable / disable column by Bulk Actions
   * @param table
   * @param enable
   * @return {Promise<textContent>}
   */
  async changeEnabledColumnBulkActions(table, enable = true) {
    // Click on Select All
    await Promise.all([
      this.page.$eval(this.selectAllRowsLabel(table), el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton(table)}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton(table)),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton(table)}`),
    ]);
    // Click on enable/disable and wait for modal
    await this.clickAndWaitForNavigation(
      enable ? this.bulkActionsEnableButton(table) : this.bulkActionsDisableButton(table),
    );
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * get text from a column
   * @param table, Pages or Categories
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(table, row, column) {
    return this.getTextContent(this.listTableColumn(table, row, column));
  }

  /**
   * Get text column form table cms page
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  getTextColumnFromTableCmsPage(row, column) {
    return this.getTextColumnFromTable('cms_page', row, column);
  }

  /**
   * Get text column form table cms page category
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  getTextColumnFromTableCmsPageCategory(row, column) {
    return this.getTextColumnFromTable('cms_page_category', row, column);
  }


  /**
   * Get content from all rows
   * @param table
   * @param column
   * @return {Promise<string[]>}
   */
  async getAllRowsColumnContent(table, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(table);
    const allRowsContentTable = [];
    let rowContent;
    for (let i = 1; i <= rowsNumber; i++) {
      if (table === 'cms_page_category') {
        rowContent = await this.getTextColumnFromTableCmsPageCategory(i, column);
      } else if (table === 'cms_page') {
        rowContent = await this.getTextColumnFromTableCmsPage(i, column);
      }
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Get content from all rows table cms page category
   * @param column
   * @return {Promise<string[]>}
   */
  getAllRowsColumnContentTableCmsPageCategory(column) {
    return this.getAllRowsColumnContent('cms_page_category', column);
  }

  /**
   * Get content from all rows table cms page
   * @param column
   * @return {Promise<string[]|int[]>}
   */
  getAllRowsColumnContentTableCmsPage(column) {
    return this.getAllRowsColumnContent('cms_page', column);
  }

  /**
   * get number of elements in grid
   * @param table
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid(table) {
    return this.getNumberFromText(this.gridTitle(table));
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param table, table to sort
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(table, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(table, sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(table, sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }

  /**
   * Sort table cms page category
   * @param sortBy
   * @param sortDirection
   * @return {Promise<void>}
   */
  async sortTableCmsPageCategory(sortBy, sortDirection = 'asc') {
    return this.sortTable('cms_page_category', sortBy, sortDirection);
  }

  /**
   * Sort table cms page
   * @param sortBy
   * @param sortDirection
   * @return {Promise<void>}
   */
  async sortTableCmsPage(sortBy, sortDirection = 'asc') {
    return this.sortTable('cms_page', sortBy, sortDirection);
  }

  // Category methods

  /**
   * Go to Edit Category page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditCategoryPage(row) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.listTableToggleDropDown('cms_page_category', row)),
      this.waitForVisibleSelector(`${this.listTableToggleDropDown('cms_page_category', row)}[aria-expanded='true']`),
    ]);
    // Click on edit
    await this.clickAndWaitForNavigation(this.listTableEditLink('cms_page_category', row));
  }

  /**
   * Go to new Page Category page
   * @return {Promise<void>}
   */
  async goToAddNewPageCategory() {
    await this.clickAndWaitForNavigation(this.addNewPageCategoryLink);
  }

  /**
   * Back to Pages page
   * @return {Promise<void>}
   */
  async backToList() {
    await this.clickAndWaitForNavigation(this.backToListButton);
  }

  /**
   * View Category
   * @param row, row in table
   * @return {Promise<void>}
   */
  async viewCategory(row) {
    await this.clickAndWaitForNavigation(this.categoriesListTableViewLink(row));
  }

  // Page methods

  /**
   * Go to new Page page
   * @return {Promise<void>}
   */
  async goToAddNewPage() {
    await this.clickAndWaitForNavigation(this.addNewPageLink);
  }

  /**
   * Go to Edit page Page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditPage(row) {
    await this.clickAndWaitForNavigation(this.listTableEditLink('cms_page', row));
  }

  /**
   * Select category pagination limit
   * @param number
   * @returns {Promise<string>}
   */
  async selectCategoryPaginationLimit(number) {
    await this.selectByVisibleText(this.categoriesPaginationLimitSelect, number);
    return this.getTextContent(this.categoriesPaginationLabel);
  }

  /**
   * Category pagination next
   * @returns {Promise<string>}
   */
  async paginationCategoryNext() {
    await this.clickAndWaitForNavigation(this.categoriesPaginationNextLink);
    return this.getTextContent(this.categoriesPaginationLabel);
  }

  /**
   * Category pagination previous
   * @returns {Promise<string>}
   */
  async paginationCategoryPrevious() {
    await this.clickAndWaitForNavigation(this.categoriesPaginationPreviousLink);
    return this.getTextContent(this.categoriesPaginationLabel);
  }

  /**
   * Select pages pagination limit
   * @param number
   * @returns {Promise<string>}
   */
  async selectPagesPaginationLimit(number) {
    await this.selectByVisibleText(this.pagesPaginationLimitSelect, number);
    return this.getTextContent(this.pagesPaginationLabel);
  }

  /**
   * Pages pagination next
   * @returns {Promise<string>}
   */
  async paginationPagesNext() {
    await this.clickAndWaitForNavigation(this.pagesPaginationNextLink);
    return this.getTextContent(this.pagesPaginationLabel);
  }

  /**
   * Pages pagination previous
   * @returns {Promise<string>}
   */
  async paginationPagesPrevious() {
    await this.clickAndWaitForNavigation(this.pagesPaginationPreviousLink);
    return this.getTextContent(this.pagesPaginationLabel);
  }
};
