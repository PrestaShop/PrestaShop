require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Pages extends BOBasePage {
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
    this.columnValidIcon = (table, row) => `${this.listTableColumn(table, row, 'active')}`
      + ' i.grid-toggler-icon-valid';

    this.columnNotValidIcon = (table, row) => `${this.listTableColumn(table, row, 'active')}`
      + ' i.grid-toggler-icon-not-valid';

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
   * @param page
   * @param table
   * @return {Promise<void>}
   */
  async resetAndGetNumberOfLines(page, table) {
    const resetButton = this.filterResetButton(table);
    if (await this.elementVisible(page, resetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, resetButton);
    }
    return this.getNumberFromText(page, this.gridHeaderTitle(table));
  }

  /**
   * Filter table
   * @param page
   * @param table
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterTable(page, table, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(table, filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(table, filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton(table));
  }

  /**
   * Delete row in table
   * @param page
   * @param table
   * @param row, row in table
   * @returns {Promise<string>}
   */
  async deleteRowInTable(page, table, row) {
    // Click on dropDown
    await Promise.all([
      page.click(this.listTableToggleDropDown(table, row)),
      this.waitForVisibleSelector(page, `${this.listTableToggleDropDown(table, row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteRowLink(table, row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal(table)}.show`),
    ]);
    await this.confirmDeleteFromTable(page, table);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all rows in table with Bulk Actions
   * @param page
   * @param table
   * @returns {Promise<string>}
   */
  async deleteWithBulkActions(page, table) {
    // Add listener to dialog to accept deletion
    this.dialogListener(page);
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel(table), el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(table)}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton(table)),
      this.waitForVisibleSelector(page, this.bulkActionsDeleteButton(table)),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.bulkActionsDeleteButton(table)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal(table)}.show`),
    ]);
    await this.confirmDeleteFromTable(page, table);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete with in modal
   * @param page
   * @param table
   * @return {Promise<void>}
   */
  async confirmDeleteFromTable(page, table) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton(table));
  }

  /**
   * Get Value of column Displayed in table
   * @param page
   * @param table
   * @param row, row in table
   * @return {Promise<boolean>}
   */
  async getStatus(page, table, row) {
    return this.elementVisible(page, this.columnValidIcon(table, row), 100);
  }

  /**
   * Quick edit toggle column value in table
   * @param page
   * @param table
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page, table, row, valueWanted = true) {
    await this.waitForVisibleSelector(page, this.listTableColumn(table, row, 'active'), 2000);
    if (await this.getStatus(page, table, row) !== valueWanted) {
      page.click(this.listTableColumn(table, row, 'active'));
      await this.waitForVisibleSelector(
        page,
        (valueWanted ? this.columnValidIcon : this.columnNotValidIcon)(table, row),
      );

      return true;
    }

    return false;
  }

  /**
   * Enable / disable column by Bulk Actions
   * @param page
   * @param table
   * @param enable
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page, table, enable = true) {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel(table), el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(table)}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton(table)),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(table)}`),
    ]);
    // Click on enable/disable and wait for modal
    await this.clickAndWaitForNavigation(
      page,
      enable ? this.bulkActionsEnableButton(table) : this.bulkActionsDisableButton(table),
    );
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * get text from a column
   * @param page
   * @param table, Pages or Categories
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(page, table, row, column) {
    return this.getTextContent(page, this.listTableColumn(table, row, column));
  }

  /**
   * Get text column form table cms page
   * @param page
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  getTextColumnFromTableCmsPage(page, row, column) {
    return this.getTextColumnFromTable(page, 'cms_page', row, column);
  }

  /**
   * Get text column form table cms page category
   * @param page
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  getTextColumnFromTableCmsPageCategory(page, row, column) {
    return this.getTextColumnFromTable(page, 'cms_page_category', row, column);
  }


  /**
   * Get content from all rows
   * @param page
   * @param table
   * @param column
   * @return {Promise<string[]>}
   */
  async getAllRowsColumnContent(page, table, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page, table);
    const allRowsContentTable = [];
    let rowContent;
    for (let i = 1; i <= rowsNumber; i++) {
      if (table === 'cms_page_category') {
        rowContent = await this.getTextColumnFromTableCmsPageCategory(page, i, column);
      } else if (table === 'cms_page') {
        rowContent = await this.getTextColumnFromTableCmsPage(page, i, column);
      }
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Get content from all rows table cms page category
   * @param page
   * @param column
   * @return {Promise<string[]>}
   */
  getAllRowsColumnContentTableCmsPageCategory(page, column) {
    return this.getAllRowsColumnContent(page, 'cms_page_category', column);
  }

  /**
   * Get content from all rows table cms page
   * @param page
   * @param column
   * @returns {Promise<string[]>}
   */
  getAllRowsColumnContentTableCmsPage(page, column) {
    return this.getAllRowsColumnContent(page, 'cms_page', column);
  }

  /**
   * get number of elements in grid
   * @param page
   * @param table
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page, table) {
    return this.getNumberFromText(page, this.gridTitle(table));
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page
   * @param table, table to sort
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

  /**
   * Sort table cms page category
   * @param page
   * @param sortBy
   * @param sortDirection
   * @return {Promise<void>}
   */
  async sortTableCmsPageCategory(page, sortBy, sortDirection = 'asc') {
    return this.sortTable(page, 'cms_page_category', sortBy, sortDirection);
  }

  /**
   * Sort table cms page
   * @param page
   * @param sortBy
   * @param sortDirection
   * @return {Promise<void>}
   */
  async sortTableCmsPage(page, sortBy, sortDirection = 'asc') {
    return this.sortTable(page, 'cms_page', sortBy, sortDirection);
  }

  // Category methods

  /**
   * Go to Edit Category page
   * @param page
   * @param row, row in table
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
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewPageCategory(page) {
    await this.clickAndWaitForNavigation(page, this.addNewPageCategoryLink);
  }

  /**
   * Back to Pages page
   * @param page
   * @return {Promise<void>}
   */
  async backToList(page) {
    await this.clickAndWaitForNavigation(page, this.backToListButton);
  }

  /**
   * View Category
   * @param page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async viewCategory(page, row) {
    await this.clickAndWaitForNavigation(page, this.categoriesListTableViewLink(row));
  }

  // Page methods

  /**
   * Go to new Page page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewPageLink);
  }

  /**
   * Go to Edit page Page
   * @param page
   * @param row, row in table
   * @return {Promise<void>}
   */
  async goToEditPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.listTableEditLink('cms_page', row));
  }

  /**
   * Select category pagination limit
   * @param page
   * @param number
   * @returns {Promise<string>}
   */
  async selectCategoryPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.categoriesPaginationLimitSelect, number);
    return this.getTextContent(page, this.categoriesPaginationLabel);
  }

  /**
   * Category pagination next
   * @param page
   * @returns {Promise<string>}
   */
  async paginationCategoryNext(page) {
    await this.clickAndWaitForNavigation(page, this.categoriesPaginationNextLink);
    return this.getTextContent(page, this.categoriesPaginationLabel);
  }

  /**
   * Category pagination previous
   * @param page
   * @returns {Promise<string>}
   */
  async paginationCategoryPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.categoriesPaginationPreviousLink);
    return this.getTextContent(page, this.categoriesPaginationLabel);
  }

  /**
   * Select pages pagination limit
   * @param page
   * @param number
   * @returns {Promise<string>}
   */
  async selectPagesPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.pagesPaginationLimitSelect, number);
    return this.getTextContent(page, this.pagesPaginationLabel);
  }

  /**
   * Pages pagination next
   * @param page
   * @returns {Promise<string>}
   */
  async paginationPagesNext(page) {
    await this.clickAndWaitForNavigation(page, this.pagesPaginationNextLink);
    return this.getTextContent(page, this.pagesPaginationLabel);
  }

  /**
   * Pages pagination previous
   * @param page
   * @returns {Promise<string>}
   */
  async paginationPagesPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.pagesPaginationPreviousLink);
    return this.getTextContent(page, this.pagesPaginationLabel);
  }
}
module.exports = new Pages();
