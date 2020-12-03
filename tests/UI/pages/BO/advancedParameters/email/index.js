require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Email extends BOBasePage {
  constructor() {
    super();

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
    this.filterSearchButton = `${this.emailsListForm} button[name='email_logs[actions][search]']`;
    this.filterResetButton = `${this.emailsListForm} button[name='email_logs[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.emailsListForm} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = row => `${this.tableRows}:nth-child(${row})`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    this.deleteRowLink = row => `${this.tableRow(row)} td.column-actions a[href*='delete']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.emailGridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.emailGridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#email_logs_grid_bulk_action_delete_email_logs';
    // Email form
    this.logEmailsLabel = toggle => `label[for='form_email_config_log_emails_${toggle}']`;
    this.saveEmailFormButton = 'form[name=\'form\'] button.btn-primary';
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
   * @param page
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get total of email created
   * @param page
   * @returns {Promise<number>}
   */
  async getTotalElementInGrid(page) {
    return this.getNumberFromText(page, this.emailGridTitle);
  }

  /**
   * Get number of elements in grid (displayed in one page)
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return (await page.$$(`${this.tableRows}:not(.empty_row)`)).length;
  }

  /**
   * Reset and get number of lines
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    return this.getTotalElementInGrid(page);
  }

  /**
   * Filter list of email logs
   * @param page
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @returns {Promise<void>}
   */
  async filterEmailLogs(page, filterType, filterBy, value = '') {
    await this.resetFilter(page);
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.emailFilterColumnInput(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(page, this.emailFilterColumnInput(filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get text from column
   * @param page
   * @param columnName
   * @param row
   * @returns {Promise<string>}
   */
  getTextColumn(page, columnName, row) {
    return this.getTextContent(page, this.tableColumn(row, columnName === 'id_lang' ? 'language' : columnName));
  }

  /**
   * Filter email logs by date
   * @param page
   * @param dateFrom
   * @param dateTo
   * @returns {Promise<void>}
   */
  async filterEmailLogsByDate(page, dateFrom, dateTo) {
    await page.type(this.emailFilterColumnInput('date_add_from'), dateFrom);
    await page.type(this.emailFilterColumnInput('date_add_to'), dateTo);
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Delete email logs
   * @param page
   * @param row
   * @returns {Promise<string>}
   */
  async deleteEmailLog(page, row) {
    this.dialogListener(page, true);
    await this.waitForSelectorAndClick(page, this.deleteRowLink(row));
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Delete email logs by bulk actions
   * @param page
   * @returns {Promise<string>}
   */
  async deleteEmailLogsBulkActions(page) {
    this.dialogListener(page, true);
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(page, this.bulkActionsDeleteButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Send a test email
   * @param page
   * @param email
   * @returns {Promise<string>}
   */
  async sendTestEmail(page, email) {
    await this.setValue(page, this.sendTestEmailInput, email);
    await page.click(this.sendTestEmailButton);
    return this.getTextContent(page, this.sendTestEmailAlertParagraph);
  }

  /**
   * Enable/Disable log emails
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setLogEmails(page, toEnable) {
    await this.waitForSelectorAndClick(page, this.logEmailsLabel(toEnable ? 1 : 0));
    await page.$eval(this.saveEmailFormButton, el => el.click());
    await page.waitForNavigation();
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Is log emails table visible
   * @param page
   * @returns {Promise<boolean>}
   */
  async isLogEmailsTableVisible(page) {
    return this.elementVisible(page, this.emailGridPanel, 1000);
  }
}

module.exports = new Email();
