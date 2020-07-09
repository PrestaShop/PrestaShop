require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Email extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'E-mail •';
    this.sendTestEmailSuccessfulMessage = 'A test email has been sent to the email address you provided.';
    this.successfulUpdateMessage = 'The settings have been successfully updated.';

    // Selectors
    // List of emails
    this.emailGridPanel = '#email_logs_grid_panel';
    this.emailGridTitle = `${this.emailGridPanel} h3.card-header-title`;
    this.emailsListForm = '#email_logs_grid_table';
    // Filters
    this.emailFilterColumnInput = filterBy => `#email_logs_${filterBy}`;
    this.filterSearchButton = `${this.emailsListForm} .grid-search-button`;
    this.filterResetButton = `${this.emailsListForm} .grid-reset-button`;
    // Table rows and columns
    this.tableBody = `${this.emailsListForm} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = row => `${this.tableRows}:nth-child(${row})`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    this.deleteRowLink = row => `${this.tableRow(row)} td.column-actions a.grid-delete-row-link`;
    this.confirmDeleteModal = '#email_logs-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.emailGridPanel} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.emailGridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#email_logs_grid_bulk_action_delete_email_logs';
    // Email form
    this.logEmailsLabel = toggle => `label[for='form_log_emails_${toggle}']`;
    this.saveEmailFormButton = '#form-log-email-save-button';
    // Test your email configuration form
    this.sendTestEmailForm = 'form[name=\'test_email_sending\']';
    this.sendTestEmailInput = '#test_email_sending_send_email_to';
    this.sendTestEmailButton = `${this.sendTestEmailForm} button.js-send-test-email-btn`;
    this.sendTestEmailAlertParagraph = `${this.sendTestEmailForm} .alert-success p.alert-text`;
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
   * Get total of email created
   * @returns {Promise<number>}
   */
  async getTotalElementInGrid() {
    return this.getNumberFromText(this.emailGridTitle);
  }

  /**
   * Get number of elements in grid (displayed in one page)
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid() {
    return (await this.page.$$(`${this.tableRows}:not(.empty_row)`)).length;
  }

  /**
   * Reset and get number of lines
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
    return this.getTotalElementInGrid();
  }

  /**
   * Filter list of email logs
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @returns {Promise<void>}
   */
  async filterEmailLogs(filterType, filterBy, value = '') {
    await this.resetFilter();
    switch (filterType) {
      case 'input':
        await this.setValue(this.emailFilterColumnInput(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.emailFilterColumnInput(filterBy), value);
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
   * @returns {Promise<string>}
   */
  getTextColumn(columnName, row) {
    return this.getTextContent(this.tableColumn(row, columnName === 'id_lang' ? 'language' : columnName));
  }

  /**
   * Filter email logs by date
   * @param dateFrom
   * @param dateTo
   * @returns {Promise<void>}
   */
  async filterEmailLogsByDate(dateFrom, dateTo) {
    await this.page.type(this.emailFilterColumnInput('date_add_from'), dateFrom);
    await this.page.type(this.emailFilterColumnInput('date_add_to'), dateTo);
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Delete email logs
   * @param row
   * @returns {Promise<string>}
   */
  async deleteEmailLog(row) {
    // Click on delete and wait for modal
    await Promise.all([
      this.waitForSelectorAndClick(this.deleteRowLink(row)),
      this.waitForVisibleSelector(`${this.confirmDeleteModal}.show`),
    ]);
    await this.clickAndWaitForNavigation(this.confirmDeleteButton);
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
      this.page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(this.bulkActionsDeleteButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Send a test email
   * @param email
   * @returns {Promise<string>}
   */
  async sendTestEmail(email) {
    await this.setValue(this.sendTestEmailInput, email);
    await this.page.click(this.sendTestEmailButton);
    return this.getTextContent(this.sendTestEmailAlertParagraph);
  }

  /**
   * Enable/Disable log emails
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setLogEmails(toEnable) {
    await this.waitForSelectorAndClick(this.logEmailsLabel(toEnable ? 1 : 0));
    await this.page.$eval(this.saveEmailFormButton, el => el.click());
    await this.page.waitForNavigation();
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Is log emails table visible
   * @returns {Promise<boolean>}
   */
  async isLogEmailsTableVisible() {
    return this.elementVisible(this.emailGridPanel, 1000);
  }
};
