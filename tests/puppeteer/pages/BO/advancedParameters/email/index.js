require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Email extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'E-mail •';

    // Selectors
    // List of emails
    this.emailGridPanel = '#email_logs_grid_panel';
    this.emailGridTitle = `${this.emailGridPanel} h3.card-header-title`;
    this.emailsListForm = '#email_logs_grid_table';
    // Filters
    this.emailFilterColumnInput = '#email_logs_%FILTERBY';
    this.filterSearchButton = `${this.emailsListForm} button[name='email_logs[actions][search]']`;
    this.filterResetButton = `${this.emailsListForm} button[name='email_logs[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.emailsListForm} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    this.deleteRowLink = `${this.tableRow} td.column-actions a[href*='delete']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.emailGridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.emailGridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#email_logs_grid_bulk_action_delete_email_logs';
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @returns {Promise<void>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.emailGridTitle);
  }

  /**
   * Reset and get number of lines
   * @returns {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
    return this.getNumberOfElementInGrid();
  }

  /**
   * Filter list of email logs
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterEmailLogs(filterType, filterBy, value = '') {
    await this.resetFilter();
    switch (filterType) {
      case 'input':
        await this.setValue(this.emailFilterColumnInput.replace('%FILTERBY', filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(
          this.emailFilterColumnInput.replace('%FILTERBY', filterBy),
          value,
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Get text from column
   * @param columnName
   * @param row
   * @return {Promise<textContent>}
   */
  getTextColumn(columnName, row) {
    if (columnName === 'id_lang') {
      return this.getTextContent(this.tableColumn.replace('%ROW', row).replace('%COLUMN', 'language'));
    }
    return this.getTextContent(this.tableColumn.replace('%ROW', row).replace('%COLUMN', columnName));
  }

  /**
   * Filter email logs by date
   * @param dateFrom
   * @param dateTo
   * @returns {Promise<void>}
   */
  async filterEmailLogsByDate(dateFrom, dateTo) {
    await this.page.type(this.emailFilterColumnInput.replace('%FILTERBY', 'date_add_from'), dateFrom);
    await this.page.type(this.emailFilterColumnInput.replace('%FILTERBY', 'date_add_to'), dateTo);
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Delete email logs
   * @param row
   * @returns {Promise<string>}
   */
  async deleteEmailLog(row) {
    this.dialogListener(true);
    await this.waitForSelectorAndClick(this.deleteRowLink.replace('%ROW', row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete email logs by bulk actions
   * @returns {Promise<string>}
   */
  async deleteEmailLogsBulkActions() {
    this.dialogListener(true);
    // Click on Select All
    await Promise.all([
      this.page.click(this.selectAllRowsLabel),
      this.waitForVisibleSelector(`${this.selectAllRowsLabel}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(this.bulkActionsToggleButton),
    ]);
    // Click on delete and wait for modal
    await this.page.click(this.bulkActionsDeleteButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
