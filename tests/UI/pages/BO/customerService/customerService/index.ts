import BOBasePage from '@pages/BO/BObasePage';

// Import data
import CustomerServiceOptionsData from '@data/faker/customerServiceOptions';

import type {Page} from 'playwright';

/**
 * Customer service page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class CustomerService extends BOBasePage {
  public readonly pageTitle: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfTitlesSpan: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableBodyRows: string;

  private readonly tableBodyRow: (row: number) => string;

  private readonly tableBodyColumn: (row: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsViewLink: (row: number) => string;

  private readonly tableColumnActionsToggleButton: (row: number) => string;

  private readonly tableColumnActionsDropdownMenu: (row: number) => string;

  private readonly tableColumnActionsDeleteLink: (row: number) => string;

  private readonly deleteModalButtonYes: string;

  private readonly tableColumn: (row: number, column: number) => string;

  private readonly tableStatusIcon: (row: number, column: number) => string;

  private readonly tableTextDangerStatusIcon: (row: number, column: number) => string;

  private readonly tableTextSuccessStatusIcon: (row: number, column: number) => string;

  private readonly tableTextWarningStatusIcon: (row: number, column: number) => string;

  public readonly deleteMessageSuccessAlertText: string;

  private readonly contactOptionForm: string;

  private readonly allowFileUploadingToggleInput: (toggle: string) => string;

  private readonly defaultMessageTextarea: string;

  private readonly contactOptionSaveButton: string;

  private readonly imapUrlInput: string;

  private readonly imapPortInput: string;

  private readonly imapUserInput: string;

  private readonly imapPasswordInput: string;

  private readonly deleteMessageToggleInput: (toggle: string) => string;

  private readonly createNewThreadToggleInput: (toggle: string) => string;

  private readonly imapOptionsSslToggleInput: (toggle: string) => string;

  private readonly customerServiceOptionsSaveButton: string;

  private readonly runSyncButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on customer service page
   */
  constructor() {
    super();

    this.pageTitle = 'Customer Service •';
    this.successfulUpdateMessage = 'The settings have been successfully updated.';

    // Form selectors
    this.gridForm = '#form-customer_thread';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-customer_thread';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='customer_threadFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtoncustomer_thread';
    this.filterResetButton = 'button[name=\'submitResetcustomer_thread\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;

    // Actions buttons in Row
    this.tableColumnActions = (row: number) => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsViewLink = (row: number) => `${this.tableColumnActions(row)} a[title='View']`;
    this.tableColumnActionsToggleButton = (row: number) => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (row: number) => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (row: number) => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Columns selector
    this.tableColumn = (row: number, column: number) => `${this.tableBodyColumn(row)}:nth-child(${column})`;
    this.tableStatusIcon = (row: number, column: number) => `${this.tableColumn(row, column)} i`;
    this.tableTextDangerStatusIcon = (row: number, column: number) => `${this.tableStatusIcon(row, column)}.text-danger`;
    this.tableTextSuccessStatusIcon = (row: number, column: number) => `${this.tableStatusIcon(row, column)}.text-success`;
    this.tableTextWarningStatusIcon = (row: number, column: number) => `${this.tableStatusIcon(row, column)}.text-warning`;

    // Delete message success text
    this.deleteMessageSuccessAlertText = 'Successful deletion';

    // Contact options selectors
    this.contactOptionForm = '#customer_thread_fieldset_contact';
    this.allowFileUploadingToggleInput = (toggle: string) => `#PS_CUSTOMER_SERVICE_FILE_UPLOAD_${toggle}`;
    this.defaultMessageTextarea = '#PS_CUSTOMER_SERVICE_SIGNATURE_1 textarea';
    this.contactOptionSaveButton = `${this.contactOptionForm} button[name='submitOptionscustomer_thread']`;

    // Customer service options selectors
    this.imapUrlInput = '#conf_id_PS_SAV_IMAP_URL input';
    this.imapPortInput = '#conf_id_PS_SAV_IMAP_PORT input';
    this.imapUserInput = '#conf_id_PS_SAV_IMAP_USER input';
    this.imapPasswordInput = '#conf_id_PS_SAV_IMAP_PWD input[type=password]';
    this.deleteMessageToggleInput = (toEnable: string) => `#PS_SAV_IMAP_DELETE_MSG_${toEnable}`;
    this.createNewThreadToggleInput = (toEnable: string) => `#PS_SAV_IMAP_CREATE_THREADS_${toEnable}`;
    this.imapOptionsSslToggleInput = (toEnable: string) => `#PS_SAV_IMAP_OPT_SSL_${toEnable}`;
    this.customerServiceOptionsSaveButton = '#customer_thread_fieldset_general div.panel-footer button';
    this.runSyncButton = '#run_sync';
  }

  /* Header Methods */

  /* Reset Methods */
  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /* filter Methods */
  /**
   * Filter table
   * @param page {Page} Browser tab
   * @param filterType {string} Type of the filter (input|select)
   * @param filterBy {string} Filter column from table
   * @param value {string} Value to set on the filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    const currentUrl: string = page.url();

    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
        await this.clickAndWaitForURL(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(filterBy), value ? 'Yes' : 'No'),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table to get text from
   * @param columnName {string} Column to get text from
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    let i: number = 0;

    if (await this.elementVisible(page, this.filterColumn('id_customer_thread'), 2000)) {
      i += 1;
    }
    let columnSelector: string;

    switch (columnName) {
      case 'id_customer_thread':
        columnSelector = this.tableColumn(row, i + 1);
        break;

      case 'customer':
        columnSelector = this.tableColumn(row, i + 2);
        break;

      case 'a!email':
        columnSelector = this.tableColumn(row, i + 3);
        break;

      case 'cl!id_contact':
        columnSelector = this.tableColumn(row, i + 4);
        break;

      case 'l!id_lang':
        columnSelector = this.tableColumn(row, i + 5);
        break;

      case 'a!status':
        columnSelector = this.tableColumn(row, i + 6);
        break;

      case 'employee':
        columnSelector = this.tableColumn(row, i + 7);
        break;

      case 'message':
        columnSelector = this.tableColumn(row, i + 8);
        break;

      case 'private':
        columnSelector = this.tableColumn(row, i + 9);
        break;

      case 'date':
        columnSelector = this.tableColumn(row, i + 10);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Is status changed
   * @param page {Page} Browser tab
   * @param row {number} Row on table to
   * @param status {string} Status to check
   * @returns {Promise<boolean>}
   */
  async isStatusChanged(page: Page, row: number, status: string): Promise<boolean> {
    let statusColumn: number = 6;

    if (await this.elementVisible(page, this.filterColumn('id_customer_thread'), 500)) {
      statusColumn += 1;
    }

    let selector: (row: number, column: number) => string;

    switch (status) {
      case 'Handled':
        selector = this.tableTextDangerStatusIcon;
        break;

      case 'Re-open':
        selector = this.tableTextSuccessStatusIcon;
        break;

      case 'Pending 1':
        selector = this.tableTextWarningStatusIcon;
        break;

      case 'Pending 2':
        selector = this.tableTextWarningStatusIcon;
        break;

      default:
        throw new Error(`${status} was not found as an option`);
    }

    return this.elementVisible(page, selector(row, statusColumn), 2000);
  }

  /**
   * Go to view message page
   * @param page {Page} Browser tab
   * @param row {number} Row on table to click on
   * @returns {Promise<void>}
   */
  async goToViewMessagePage(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsViewLink(row));
  }

  /**
   * Delete message
   * @param page {Page} Browser tab
   * @param row {number} Row on table to delete
   * @returns {Promise<string>}
   */
  async deleteMessage(page: Page, row: number): Promise<string> {
    await Promise.all([
      page.locator(this.tableColumnActionsToggleButton(row)).click(),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.locator(this.tableColumnActionsDeleteLink(row)).click();

    // Confirm delete action
    await page.locator(this.deleteModalButtonYes).click();
    await this.elementNotVisible(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Enable/Disable allow file uploading
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable allow file uploading
   * @returns {Promise<string>}
   */
  async allowFileUploading(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.allowFileUploadingToggleInput(toEnable ? 'on' : 'off'));
    await page.locator(this.contactOptionSaveButton).click();
    await this.elementNotVisible(page, this.allowFileUploadingToggleInput(!toEnable ? 'on' : 'off'));

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Set default message
   * @param page {Page} Browser tab
   * @param message {string} Value to set on message textarea
   * @returns {Promise<string>}
   */
  async setDefaultMessage(page: Page, message: string): Promise<string> {
    await page.locator(this.defaultMessageTextarea).fill(message);
    await page.locator(this.contactOptionSaveButton).click();

    return this.getAlertSuccessBlockContent(page);
  }

  // Methods for customer service options
  /**
   * Set customer service options
   * @param page {Page} Browser tab
   * @param optionsData {CustomerServiceOptionsData} Data to set in customer service options form
   * @returns {Promise<string>}
   */
  async setCustomerServiceOptions(page: Page, optionsData: CustomerServiceOptionsData): Promise<string> {
    await page.locator(this.imapUrlInput).fill(optionsData.imapUrl);
    await page.locator(this.imapPortInput).fill(optionsData.imapPort);
    await page.locator(this.imapUserInput).fill(optionsData.imapUser);
    await page.locator(this.imapPasswordInput).fill(optionsData.imapPassword);
    await this.setChecked(page, this.deleteMessageToggleInput(optionsData.deleteMessage ? 'on' : 'off'));
    await this.setChecked(page, this.createNewThreadToggleInput(optionsData.createNewThreads ? 'on' : 'off'));
    await this.setChecked(page, this.imapOptionsSslToggleInput(optionsData.imapOptionsSsl ? 'on' : 'off'));
    await page.locator(this.customerServiceOptionsSaveButton).click();

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Is run sync button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isRunSyncButtonVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.runSyncButton, 2000);
  }
}

export default new CustomerService();
