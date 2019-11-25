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
    this.listTableRow = `${this.listForm} tbody tr:nth-child(%ROW)`;
    this.listTableColumn = `${this.listTableRow} td.column-%COLUMN`;
    this.columnValidIcon = `${this.listTableColumn.replace('%COLUMN', 'active')} i.grid-toggler-icon-valid`;
    this.columnNotValidIcon = `${this.listTableColumn.replace('%COLUMN', 'active')} i.grid-toggler-icon-not-valid`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.listForm} .md-checkbox label`;
    this.bulkActionsToggleButton = `${this.listForm} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = `${this.listForm} #%TABLE_grid_bulk_action_delete_bulk`;
    this.bulkActionsEnableButton = `${this.listForm} #%TABLE_grid_bulk_action_enable_selection`;
    this.bulkActionsDisableButton = `${this.listForm} #%TABLE_grid_bulk_action_disable_selection`;
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
      this.page.waitForSelector(`${listTableToggleDropDown
        .replace('%ROW', row)}[aria-expanded='true']`, {visible: true},
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(deleteRowLink.replace('%ROW', row)),
      this.dialogListener(),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all rows in table with Bulk Actions
   * @param table
   * @return {Promise<textContent>}
   */
  async deleteRowInTableBulkActions(table) {
    const selectAllRowsLabel = await this.replaceAll(this.selectAllRowsLabel, '%TABLE', table);
    const bulkActionsToggleButton = await this.replaceAll(this.bulkActionsToggleButton, '%TABLE', table);
    const bulkActionsDeleteButton = await this.replaceAll(this.bulkActionsDeleteButton, '%TABLE', table);
    // Add listener to dialog to accept deletion
    this.dialogListener();
    // Click on Select All
    await Promise.all([
      this.page.click(selectAllRowsLabel),
      this.page.waitForSelector(`${selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(bulkActionsToggleButton),
      this.page.waitForSelector(`${bulkActionsToggleButton}`, {visible: true}),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(bulkActionsDeleteButton),
      this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true}),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
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
    if (await this.getToggleColumnValue(table, row) !== valueWanted) {
      this.page.click(this.listTableColumn
        .replace('%TABLE', table)
        .replace('%ROW', row)
        .replace('%COLUMN', 'active'),
      );
      if (valueWanted) {
        await this.page.waitForSelector(this.columnValidIcon
          .replace('%TABLE', table)
          .replace('%ROW', row),
        );
      } else {
        await this.page.waitForSelector(this.columnNotValidIcon
          .replace('%TABLE', table)
          .replace('%ROW', row),
        );
      }
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
      this.page.waitForSelector(`${selectAllRowsLabel}:not([disabled])`, {visible: true}),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(bulkActionsToggleButton),
      this.page.waitForSelector(`${bulkActionsToggleButton}`, {visible: true}),
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
   * @return {Promise<textContent>}
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
   * get number of elements in grid
   * @param table
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid(table) {
    return this.getNumberFromText(this.gridTitle.replace('%TABLE', table));
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
      this.page.waitForSelector(`${this.listTableToggleDropDown
        .replace('%TABLE', 'cms_page_category')
        .replace('%ROW', row)}[aria-expanded='true']`, {visible: true},
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
