require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class CustomerService extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Customer Service •';

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
  }

  /* Header Methods */

  /* Reset Methods */
  /**
   * Reset filters in table
   * @param page
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
   * @param page
   * @param filterType
   * @param filterBy
   * @param value
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
   * @param page
   * @param row
   * @param columnName
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
   * @param page
   * @param row
   * @param statusClass
   * @returns {Promise<boolean>}
   */
  async isStatusChanged(page, row = 1, statusClass) {
    let i = 6;
    if (await this.elementVisible(page, this.filterColumn('id_customer_thread'), 2000)) {
      i += 1;
    }
    return this.elementVisible(page, `${this.tableColumn(row, i)} i.${statusClass}`, 2000);
  }

  /**
   * Go to view message page
   * @param page
   * @param row
   * @returns {Promise<void>}
   */
  async goToViewMessagePage(page, row = 1) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsViewLink(row));
  }

  /**
   * Delete message
   * @param page
   * @param row
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
}

module.exports = new CustomerService();
