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

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnCustomer = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnEmail = row => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnType = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnLanguage = row => `${this.tableBodyColumn(row)}:nth-child(6)`;
    this.tableColumnStatus = row => `${this.tableBodyColumn(row)}:nth-child(7)`;
    this.tableColumnEmployee = row => `${this.tableBodyColumn(row)}:nth-child(8)`;
    this.tableColumnMessages = row => `${this.tableBodyColumn(row)}:nth-child(9)`;
    this.tableColumnPrivate = row => `${this.tableBodyColumn(row)}:nth-child(10)`;
    this.tableColumnDate = row => `${this.tableBodyColumn(row)}:nth-child(11)`;
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
    let columnSelector;

    switch (columnName) {
      case 'id_customer_thread':
        columnSelector = this.tableColumnId(row);
        break;

      case 'customer':
        columnSelector = this.tableColumnCustomer(row);
        break;

      case 'a!email':
        columnSelector = this.tableColumnEmail(row);
        break;

      case 'cl!id_contact':
        columnSelector = this.tableColumnType(row);
        break;

      case 'l!id_lang':
        columnSelector = this.tableColumnLanguage(row);
        break;

      case 'a!status':
        columnSelector = this.tableColumnStatus(row);
        break;

      case 'employee':
        columnSelector = this.tableColumnEmployee(row);
        break;

      case 'message':
        columnSelector = this.tableColumnMessages(row);
        break;

      case 'private':
        columnSelector = this.tableColumnPrivate(row);
        break;

      case 'active':
        columnSelector = this.tableColumnDate(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
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
}

module.exports = new CustomerService();
