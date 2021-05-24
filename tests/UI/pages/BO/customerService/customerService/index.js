require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Customer service page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class CustomerService extends BOBasePage {
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
    this.filterColumn = filterBy => `${this.filterRow} [name='customer_threadFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtoncustomer_thread';
    this.filterResetButton = 'button[name=\'submitResetcustomer_thread\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Actions buttons in Row
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsViewLink = row => `${this.tableColumnActions(row)} a[title='View']`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Columns selector
    this.tableColumn = (row, column) => `${this.tableBodyColumn(row)}:nth-child(${column})`;
    this.tableStatusIcon = (row, column) => `${this.tableColumn(row, column)} i`;
    this.tableTextDangerStatusIcon = (row, column) => `${this.tableStatusIcon(row, column)}.text-danger`;
    this.tableTextSuccessStatusIcon = (row, column) => `${this.tableStatusIcon(row, column)}.text-success`;
    this.tableTextWarningStatusIcon = (row, column) => `${this.tableStatusIcon(row, column)}.text-warning`;

    // Delete message success text
    this.deleteMessageSuccessAlertText = 'Successful deletion.';

    // Contact options selectors
    this.contactOptionForm = '#customer_thread_fieldset_contact';
    this.allowFileUploadingToggleInput = toggle => `#PS_CUSTOMER_SERVICE_FILE_UPLOAD_${toggle}`;
    this.defaultMessageTextarea = '#PS_CUSTOMER_SERVICE_SIGNATURE_1 textarea';
    this.contactOptionSaveButton = `${this.contactOptionForm} button[name='submitOptionscustomer_thread']`;
  }

  /* Header Methods */

  /* Reset Methods */
  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
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
  async filterTable(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
        await this.clickAndWaitForNavigation(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForNavigation({waitUntil: 'networkidle'}),
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
  async getTextColumn(page, row, columnName) {
    let i = 0;
    if (await this.elementVisible(page, this.filterColumn('id_customer_thread'), 2000)) {
      i += 1;
    }
    let columnSelector;

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
  async isStatusChanged(page, row = 1, status) {
    let statusColumn = 6;
    if (await this.elementVisible(page, this.filterColumn('id_customer_thread'), 500)) {
      statusColumn += 1;
    }

    let selector;
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
  async goToViewMessagePage(page, row = 1) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsViewLink(row));
  }

  /**
   * Delete message
   * @param page {Page} Browser tab
   * @param row {number} Row on table to delete
   * @returns {Promise<string>}
   */
  async deleteMessage(page, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Enable/Disable allow file uploading
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable allow file uploading
   * @returns {Promise<string>}
   */
  async allowFileUploading(page, toEnable = true) {
    await page.check(this.allowFileUploadingToggleInput(toEnable ? 'on' : 'off'));
    await this.clickAndWaitForNavigation(page, this.contactOptionSaveButton);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Set default message
   * @param page {Page} Browser tab
   * @param message {string} Value to set on message textarea
   * @returns {Promise<string>}
   */
  async setDefaultMessage(page, message) {
    await page.fill(this.defaultMessageTextarea, message);
    await this.clickAndWaitForNavigation(page, this.contactOptionSaveButton);

    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new CustomerService();
