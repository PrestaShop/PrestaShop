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
    this.gridPanel = '#%TABLE_grid_panel';
    this.gridTitle = `${this.gridPanel} h3.card-header-title`;
    this.gridTable = '#%TABLE_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    this.listForm = '#%TABLE_grid';
    // Sort Selectors
    this.tableHead = `${this.listForm} thead`;
    this.sortColumnDiv = `${this.tableHead} div.ps-sortable-column[data-sort-col-name='%COLUMN']`;
    this.sortColumnSpanButton = `${this.sortColumnDiv} span.ps-sort`;
    this.listTableRow = `${this.listForm} tbody tr:nth-child(%ROW)`;
    this.listTableColumn = `${this.listTableRow} td.column-%COLUMN`;
    this.columnValidIcon = `${this.listTableColumn.replace('%COLUMN', 'active')} i.grid-toggler-icon-valid`;
    this.columnNotValidIcon = `${this.listTableColumn.replace('%COLUMN', 'active')} i.grid-toggler-icon-not-valid`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.listForm} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.listForm} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#%TABLE_grid_bulk_action_delete_selection';
    this.bulkActionsEnableButton = '#%TABLE_grid_bulk_action_enable_selection';
    this.bulkActionsDisableButton = '#%TABLE_grid_bulk_action_disable_selection';
    this.confirmDeleteModal = '#%TABLE_grid_confirm_modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
    // Filters
    this.filterColumn = `${this.gridTable} #%TABLE_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='%TABLE[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='%TABLE[actions][reset]']`;
    // Actions buttons in Row
    this.listTableToggleDropDown = `${this.listTableColumn.replace('%COLUMN', 'actions')} a[data-toggle='dropdown']`;
    this.listTableEditLink = `${this.listTableColumn.replace('%COLUMN', 'actions')} a[href*='edit']`;
    this.deleteRowLink = `${this.listTableColumn.replace('%COLUMN', 'actions')} a[data-method="DELETE"]`;

    // Categories selectors
    this.backToListButton = '#cms_page_category_grid_panel div.card-footer a';
    this.categoriesListTableViewLink = `${this.listTableColumn.replace('%TABLE', 'cms_page_category')
      .replace('%COLUMN', 'actions')} a[data-original-title='View']`;
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
    const resetButton = await this.replaceAll(this.filterResetButton, '%TABLE', table);
    if (await this.elementVisible(resetButton, 2000)) {
      await this.clickAndWaitForNavigation(resetButton);
    }
    return this.getNumberFromText(this.gridHeaderTitle.replace('%TABLE', table));
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
    const filterColumn = await this.replaceAll(this.filterColumn, '%TABLE', table);
    const filterSearchButton = await this.replaceAll(this.filterSearchButton, '%TABLE', table);
    switch (filterType) {
      case 'input':
        await this.setValue(filterColumn.replace('%FILTERBY', filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(filterColumn.replace('%FILTERBY', filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(filterSearchButton),
    ]);
  }

  /**
   * Delete row in table
   * @param table
   * @param row, row in table
   * @return {Promise<textContent>}
   */
  async deleteRowInTable(table, row) {
    const listTableToggleDropDown = await this.replaceAll(this.listTableToggleDropDown, '%TABLE', table);
    const deleteRowLink = await this.replaceAll(this.deleteRowLink, '%TABLE', table);
    // Click on dropDown
    await Promise.all([
      this.page.click(listTableToggleDropDown.replace('%ROW', row)),
      this.waitForVisibleSelector(`${listTableToggleDropDown}[aria-expanded='true']`.replace('%ROW', row)),
    ]);
    // Click on delete and wait for modal
    this.dialogListener();
    await this.clickAndWaitForNavigation(deleteRowLink.replace('%ROW', row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all rows in table with Bulk Actions
   * @param table
   * @return {Promise<textContent>}
   */
  async deleteWithBulkActions(table) {
    const selectAllRowsLabel = await this.replaceAll(this.selectAllRowsLabel, '%TABLE', table);
    const bulkActionsToggleButton = await this.replaceAll(this.bulkActionsToggleButton, '%TABLE', table);
    const bulkActionsDeleteButton = await this.replaceAll(this.bulkActionsDeleteButton, '%TABLE', table);
    const confirmDeleteModal = await this.replaceAll(this.confirmDeleteModal, '%TABLE', table);
    // Add listener to dialog to accept deletion
    this.dialogListener();
    // Click on Select All
    await Promise.all([
      this.page.click(selectAllRowsLabel),
      this.waitForVisibleSelector(`${selectAllRowsLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(bulkActionsToggleButton),
      this.waitForVisibleSelector(`${bulkActionsToggleButton}`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(bulkActionsDeleteButton),
      this.waitForVisibleSelector(`${confirmDeleteModal}.show`),
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
    await this.clickAndWaitForNavigation(this.confirmDeleteButton.replace('%TABLE', table));
  }

  /**
   * Get Value of column Displayed in table
   * @param table
   * @param row, row in table
   * @return {Promise<boolean|true>}
   */
  async getToggleColumnValue(table, row) {
    return this.elementVisible(this.columnValidIcon.replace('%TABLE', table).replace('%ROW', row), 100);
  }

  /**
   * Quick edit toggle column value in table
   * @param table
   * @param row, row in table
   * @param valueWanted, Value wanted in column
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async updateToggleColumnValue(table, row, valueWanted = true) {
    await this.waitForVisibleSelector(
      this.listTableColumn.replace('%TABLE', table).replace('%ROW', row).replace('%COLUMN', 'active'),
      2000,
    );
    if (await this.getToggleColumnValue(table, row) !== valueWanted) {
      this.page.click(this.listTableColumn
        .replace('%TABLE', table)
        .replace('%ROW', row)
        .replace('%COLUMN', 'active'),
      );
      await this.waitForVisibleSelector(
        (valueWanted ? this.columnValidIcon : this.columnNotValidIcon)
          .replace('%TABLE', table)
          .replace('%ROW', row),
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
    const selectAllRowsLabel = await this.replaceAll(this.selectAllRowsLabel, '%TABLE', table);
    const bulkActionsToggleButton = await this.replaceAll(this.bulkActionsToggleButton, '%TABLE', table);
    const bulkActionsEnableButton = await this.replaceAll(this.bulkActionsEnableButton, '%TABLE', table);
    const bulkActionsDisableButton = await this.replaceAll(this.bulkActionsDisableButton, '%TABLE', table);
    // Click on Select All
    await Promise.all([
      this.page.click(selectAllRowsLabel),
      this.waitForVisibleSelector(`${selectAllRowsLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(bulkActionsToggleButton),
      this.waitForVisibleSelector(`${bulkActionsToggleButton}`),
    ]);
    // Click on enable/disable and wait for modal
    await this.clickAndWaitForNavigation(enable ? bulkActionsEnableButton : bulkActionsDisableButton);
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
    return this.getTextContent(
      this.listTableColumn
        .replace('%TABLE', table)
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
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
    return this.getNumberFromText(this.gridTitle.replace('%TABLE', table));
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
    const sortColumnDiv = `${this.sortColumnDiv}[data-sort-direction='${sortDirection}']`
      .replace('%COLUMN', sortBy)
      .replace('%TABLE', table);
    const sortColumnSpanButton = this.sortColumnSpanButton
      .replace('%COLUMN', sortBy)
      .replace('%TABLE', table);
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
      this.page.click(this.listTableToggleDropDown
        .replace('%TABLE', 'cms_page_category')
        .replace('%ROW', row),
      ),
      this.waitForVisibleSelector(`${this.listTableToggleDropDown}[aria-expanded='true']`
        .replace('%TABLE', 'cms_page_category')
        .replace('%ROW', row),
      ),
    ]);
    // Click on edit
    this.clickAndWaitForNavigation(this.listTableEditLink
      .replace('%TABLE', 'cms_page_category')
      .replace('%ROW', row));
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
    await this.clickAndWaitForNavigation(this.categoriesListTableViewLink.replace('%ROW', row));
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
    await this.clickAndWaitForNavigation(this.listTableEditLink
      .replace('%TABLE', 'cms_page').replace('%ROW', row),
    );
  }
};
