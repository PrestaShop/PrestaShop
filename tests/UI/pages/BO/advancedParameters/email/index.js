require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Email page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Email extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on email page
   */
  constructor() {
    super();

    this.pageTitle = 'E-mail •';
    this.sendTestEmailSuccessfulMessage = 'A test email has been sent to the email address you provided.';
    this.successfulUpdateMessage = 'The settings have been successfully updated.';
    this.successfulDeleteMessage = 'Successful deletion';

    // Selectors
    // List of emails
    this.emailGridPanel = '#email_logs_grid_panel';
    this.emailGridTitle = `${this.emailGridPanel} h3.card-header-title`;
    this.emailsListForm = '#email_logs_grid_table';

    // Filters
    this.emailFilterColumnInput = filterBy => `#email_logs_${filterBy}`;
    this.filterSearchButton = `${this.emailsListForm} .grid-search-button`;
    this.filterResetButton = `${this.emailsListForm} .grid-reset-button`;
    this.gridActionButton = '#email_logs-grid-actions-button';
    this.eraseAllButton = '#email_logs_grid_action_delete_all_email_logs';

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
    this.bulkActionsDeleteButton = '#email_logs_grid_bulk_action_delete_selection';

    // Email form
    this.logEmailsToggleInput = toggle => `#form_log_emails_${toggle}`;
    this.saveEmailFormButton = '#form-log-email-save-button';

    // Email form Radio buttons
    this.sendMailParametersRadioButton = '#form_mail_method_0';
    this.smtpParametersRadioButton = '#form_mail_method_1';

    // Email form input fields
    this.smtpServerFormField = '#form_smtp_config_server';
    this.smtpUsernameFormField = '#form_smtp_config_username';
    this.smtpPasswordFormField = '#form_smtp_config_password';
    this.smtpPortFormField = '#form_smtp_config_port';
    this.smtpEncryptionFormField = '#form_smtp_config_encryption';

    // Test your email configuration form
    this.sendTestEmailForm = 'form[name=\'test_email_sending\']';
    this.sendTestEmailInput = '#test_email_sending_send_email_to';
    this.sendTestEmailButton = `${this.sendTestEmailForm} button.js-send-test-email-btn`;
    this.sendTestEmailAlertParagraph = `${this.sendTestEmailForm} .alert-success p.alert-text`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.emailGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.emailGridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.emailGridPanel} [aria-label='Previous']`;

    // Sort Selectors
    this.tableHead = `${this.emailsListForm} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get total of email created
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getTotalElementInGrid(page) {
    return this.getNumberFromText(page, this.emailGridTitle);
  }

  /**
   * Get number of elements in grid (displayed in one page)
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return (await page.$$(`${this.tableRows}:not(.empty_row)`)).length;
  }

  /**
   * Reset and get number of lines
   * @param page {Page} Browser tab
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
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @returns {Promise<void>}
   */
  async filterEmailLogs(page, filterType, filterBy, value = '') {
    await this.resetFilter(page);
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.emailFilterColumnInput(filterBy), value);
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
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get text content
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  getTextColumn(page, columnName, row) {
    return this.getTextContent(page, this.tableColumn(row, columnName === 'id_lang' ? 'language' : columnName));
  }

  /**
   * Filter email logs by date
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value of date from to filter with
   * @param dateTo {string} Value of date to to filter with
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
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteEmailLog(page, row) {
    // Click on delete and wait for modal
    await Promise.all([
      this.waitForSelectorAndClick(page, this.deleteRowLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete email logs by bulk actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteEmailLogsBulkActions(page) {
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

    // Bulk delete email logs
    await Promise.all([
      this.waitForSelectorAndClick(page, this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Setup the smtp parameters
   * @param page {Page} Browser tab
   * @param server {string} Value of server name to set on smtp server input
   * @param username {string} Value of username to set on smtp username input
   * @param pass {string} Value of password to set on smtp password input
   * @param port {string} Value of port to set on smtp port input
   * @param encryption {string} Value of encryption to select from smtp encryption select
   * @returns {Promise<string>}
   */
  async setupSmtpParameters(page, server, username, pass, port, encryption = 'None') {
    // Click on smtp radio button
    await page.click(this.smtpParametersRadioButton);
    await this.waitForVisibleSelector(page, this.smtpServerFormField);
    // fill the form field
    await this.setValue(page, this.smtpServerFormField, server);
    await this.setValue(page, this.smtpUsernameFormField, username);
    await this.setValue(page, this.smtpPasswordFormField, pass);
    await this.setValue(page, this.smtpPortFormField, port);
    await this.selectByVisibleText(page, this.smtpEncryptionFormField, encryption);
    // Click on Save button
    await this.clickAndWaitForNavigation(page, this.saveEmailFormButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Reset the mail parameters to default config
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async resetDefaultParameters(page) {
    // Click on smtp radio button
    await page.click(this.sendMailParametersRadioButton);
    await this.waitForHiddenSelector(page, this.smtpServerFormField);

    // Click on Save button
    await this.clickAndWaitForNavigation(page, this.saveEmailFormButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Send a test email
   * @param page {Page} Browser tab
   * @param email {string} Value of email to set on email input
   * @returns {Promise<string>}
   */
  async sendTestEmail(page, email) {
    await this.setValue(page, this.sendTestEmailInput, email);
    await page.click(this.sendTestEmailButton);
    return this.getTextContent(page, this.sendTestEmailAlertParagraph);
  }

  /**
   * Enable/Disable log emails
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable log emails, false if not
   * @returns {Promise<string>}
   */
  async setLogEmails(page, toEnable) {
    await page.check(this.logEmailsToggleInput(toEnable ? 1 : 0));
    await page.$eval(this.saveEmailFormButton, el => el.click());
    await page.waitForNavigation();

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Is log emails table visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isLogEmailsTableVisible(page) {
    return this.elementVisible(page, this.emailGridPanel, 1000);
  }

  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForNavigation({waitUntil: 'networkidle'}),
    ]);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows column
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, column, i);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.hover(this.sortColumnDiv(sortBy));
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Erase all emails
   * @param page
   * @returns {Promise<string>}
   */
  async eraseAllEmails(page) {
    // Add listener to dialog to accept erase
    this.dialogListener(page);

    await page.click(this.gridActionButton);
    await this.waitForSelectorAndClick(page, this.eraseAllButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new Email();
