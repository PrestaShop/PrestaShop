require('module-alias/register');
const LocalizationBasePage = require('@pages/BO/international/localization/localizationBasePage');

module.exports = class Languages extends LocalizationBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Languages •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Header selectors
    this.addNewLanguageLink = '#page-header-desc-configuration-add';
    // Selectors grid panel
    this.gridPanel = '#language_grid_panel';
    this.gridTable = '#language_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Filters
    this.filterColumn = `${this.gridTable} #language_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='language[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='language[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    // Column actions selectors
    this.actionsColumn = `${this.tableRow} td.column-actions`;
    this.editRowLink = `${this.actionsColumn} a[data-original-title='Edit']`;
    this.dropdownToggleButton = `${this.actionsColumn} a.dropdown-toggle`;
    this.dropdownToggleMenu = `${this.actionsColumn} div.dropdown-menu`;
    this.deleteRowLink = `${this.dropdownToggleMenu} a[data-url*='/delete']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} .md-checkbox label`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsEnableButton = '#language_grid_bulk_action_enable_selection';
    this.bulkActionsDisableButton = '#language_grid_bulk_action_disable_selection';
    this.bulkActionsDeleteButton = '#language_grid_bulk_action_delete_selection';
    this.confirmDeleteModal = '#language_grid_confirm_modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
  }

  /* Header methods */
  /**
   * Go to add new language page
   * @return {Promise<void>}
   */
  async goToAddNewLanguage() {
    await this.clickAndWaitForNavigation(this.addNewLanguageLink);
  }

  /* Reset methods */
  /**
   * Reset filters in table
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
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

  /* Filter method */
  /**
   * Filter Table
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(this.filterColumn.replace('%FILTERBY', filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.filterColumn.replace('%FILTERBY', filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /* Table methods */
  /**
   * Get text from a column
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

  /**
   * Go to edit language page
   * @param row, which row of the list
   * @return {Promise<void>}
   */
  async goToEditLanguage(row = 1) {
    await this.clickAndWaitForNavigation(this.editRowLink.replace('%ROW', row));
  }

  /**
   * Delete Row in table
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteLanguage(row = 1) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`.replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink.replace('%ROW', row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /* Bulk Actions Methods */
  /**
   * Enable / disable Suppliers by Bulk Actions
   * @param toEnable
   * @return {Promise<textContent>}
   */
  async bulkEditEnabledColumn(toEnable = true) {
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
    await this.clickAndWaitForNavigation(toEnable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

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
    await Promise.all([
      this.page.click(this.bulkActionsDeleteButton),
      this.page.waitForSelector(`${this.confirmDeleteModal}.show`, {visible: true}),
    ]);
    await this.confirmDeleteLanguages(this.bulkActionsDeleteButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete with in modal
   * @return {Promise<void>}
   */
  async confirmDeleteLanguages() {
    await this.clickAndWaitForNavigation(this.confirmDeleteButton);
  }
};
