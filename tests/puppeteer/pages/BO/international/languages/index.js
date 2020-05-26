require('module-alias/register');
const LocalizationBasePage = require('@pages/BO/international/localization/localizationBasePage');

module.exports = class Languages extends LocalizationBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Languages •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';
    this.unSuccessfulUpdateDefaultLanguageStatusMessage = 'You cannot change the status of the default language.';

    // Header selectors
    this.addNewLanguageLink = '#page-header-desc-configuration-add';
    // Selectors grid panel
    this.gridPanel = '#language_grid_panel';
    this.gridTable = '#language_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Filters
    this.filterColumn = filterBy => `${this.gridTable} #language_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} button[name='language[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='language[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    // Column actions selectors
    this.actionsColumn = row => `${this.tableRow(row)} td.column-actions`;
    this.editRowLink = row => `${this.actionsColumn(row)} a[data-original-title='Edit']`;
    this.dropdownToggleButton = row => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = row => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a[data-url*='/delete']`;
    this.enabledColumnValidIcon = row => `${this.tableColumn(row, 'active')} i.grid-toggler-icon-valid`;
    this.enabledColumnNotValidIcon = row => `${this.tableColumn(row, 'active')} i.grid-toggler-icon-valid`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsEnableButton = '#language_grid_bulk_action_enable_selection';
    this.bulkActionsDisableButton = '#language_grid_bulk_action_disable_selection';
    this.bulkActionsDeleteButton = '#language_grid_bulk_action_delete_selection';
    this.confirmDeleteModal = '#language-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
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
        await this.setValue(this.filterColumn(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.filterColumn(filterBy), value ? 'Yes' : 'No');
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
    return this.getTextContent(this.tableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(column) {
    const rowsNumber = await this.getNumberOfElementInGrid();
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTable(i, column);
      await allRowsContentTable.push(rowContent);
    }
    return allRowsContentTable;
  }

  /**
   * Go to edit language page
   * @param row, which row of the list
   * @return {Promise<void>}
   */
  async goToEditLanguage(row = 1) {
    await this.clickAndWaitForNavigation(this.editRowLink(row));
  }

  /**
   * Delete Row in table
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteLanguage(row = 1) {
    await Promise.all([
      this.page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(
        `${this.dropdownToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.deleteRowLink(row)),
      this.waitForVisibleSelector(`${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteLanguages(this.bulkActionsDeleteButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }


  /**
   * Get language status
   * @param row
   * @return {Promise<string>}
   */
  isEnabled(row) {
    return this.elementVisible(this.enabledColumnValidIcon(row), 100);
  }

  /**
   * Enable/Disable language
   * @param row
   * @param valueWanted
   * @return {Promise<bool>}, true if click has been performed
   */
  async quickEditLanguage(row, valueWanted = true) {
    await this.waitForVisibleSelector(this.tableColumn(row, 'active'), 2000);
    if (await this.isEnabled(row) !== valueWanted) {
      await this.clickAndWaitForNavigation(this.tableColumn(row, 'active'));
      return true;
    }
    return false;
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
      this.waitForVisibleSelector(`${this.selectAllRowsLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
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
      this.waitForVisibleSelector(`${this.selectAllRowsLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(`${this.confirmDeleteModal}.show`),
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

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);
    let i = 0;
    while (await this.elementNotVisible(sortColumnDiv, 1000) && i < 2) {
      await this.clickAndWaitForNavigation(sortColumnSpanButton);
      i += 1;
    }
    await this.waitForVisibleSelector(sortColumnDiv);
  }
};
