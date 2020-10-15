require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Statuses extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Statuses •';

    // Header selectors
    this.newOrderStatusLink = '#page-header-desc-order_return_state-new_order_state';

    // Selectors
    // Form selectors
    this.gridForm = '#form-order_state';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-order_state';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='order_stateFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonorder_state';
    this.filterResetButton = 'button[name=\'submitResetorder_state\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = row => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = row => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnSendEmail = row => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnDelivery = row => `${this.tableBodyColumn(row)}:nth-child(6)`;
    this.tableColumnInvoice = row => `${this.tableBodyColumn(row)}:nth-child(7)`;
    this.tableColumnTemplate = row => `${this.tableBodyColumn(row)}:nth-child(8)`;

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';
  }

  /* Header methods */

  /**
   * Go to new orders status page
   * @param page
   * @return {Promise<void>}
   */
  async goToNewOrderStatusPage(page) {
    await this.clickAndWaitForNavigation(page, this.newOrderStatusLink);
  }

  /* Filter methods */

  /**
   * Get Number of order statuses
   * @param page
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset all filters
   * @param page
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Reset and get number of lines
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter order statuses
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

  /* Column methods */

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
      case 'id_order_state':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'send_email':
        columnSelector = this.tableColumnSendEmail(row);
        break;

      case 'delivery':
        columnSelector = this.tableColumnDelivery(row);
        break;

      case 'invoice':
        columnSelector = this.tableColumnInvoice(row);
        break;

      case 'template':
        columnSelector = this.tableColumnTemplate(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Go to edit order status page
   * @param page
   * @param row
   * @return {Promise<void>}
   */
  async gotoEditOrderStatusPage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete order status from row
   * @param page
   * @param row
   * @return {Promise<string>}
   */
  async deleteOrderStatus(page, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new Statuses();
