require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Order extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Orders •';

    // Selectors grid panel
    this.gridPanel = '#order_grid_panel';
    this.gridTable = '#order_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Filters
    this.filterColumn = `${this.gridTable} #order_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='order[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='order[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    this.updateStatusInTablebutton = `${this.tableRow} td.column-osname button`;
    // Column actions selectors
    this.actionsColumn = `${this.tableRow} td.column-actions`;
    this.viewRowLink = `${this.actionsColumn} a[data-original-title='View']`;
    // Grid Actions
    this.gridActionButton = '#order-grid-actions-button';
    this.gridActionDropDownMenu = 'div.dropdown-menu[aria-labelledby=\'order-grid-actions-button\']';
    this.gridActionExportLink = `${this.gridActionDropDownMenu} a[href*='/export']`;
  }

  /*
  Methods
   */
  /**
   * Click on lint to export orders to a csv file
   * @return {Promise<void>}
   */
  async exportDataToCsv() {
    await Promise.all([
      this.page.click(this.gridActionButton),
      this.waitForVisibleSelector(`${this.gridActionDropDownMenu}.show`),
    ]);
    await Promise.all([
      this.page.click(this.gridActionExportLink),
      this.page.waitForSelector(`${this.gridActionDropDownMenu}.show`, {hidden: true}),
    ]);
  }

  /**
   * Filter Orders
   * @param filterType
   * @param filterBy
   * @param value
   * @return {Promise<void>}
   */
  async filterOrders(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.filterColumn.replace('%FILTERBY', filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(this.filterColumn.replace('%FILTERBY', filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Reset filter in orders
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
  }

  /**
   * Get number of orders in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.gridHeaderTitle);
  }

  /**
   * Go to orders Page
   * @param orderRow
   * @return {Promise<void>}
   */
  async goToOrder(orderRow) {
    await this.clickAndWaitForNavigation(this.viewRowLink.replace('%ROW', orderRow));
  }

  /**
   * Get text from Column
   * @param columnName
   * @param row
   * @return {Promise<textContent>}
   */
  async getTextColumn(columnName, row) {
    if (columnName === 'osname') {
      return this.getTextContent(this.updateStatusInTablebutton.replace('%ROW', row));
    }
    return this.getTextContent(
      this.tableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', columnName),
    );
  }

  /**
   * Get all row information from orders table
   * @param row
   * @return {Promise<object>}
   */
  async getOrderFromTable(row) {
    return {
      id: parseFloat(await this.getTextColumn('id_order', row)),
      reference: await this.getTextColumn('reference', row),
      newClient: await this.getTextColumn('new', row),
      delivery: await this.getTextColumn('country_name', row),
      customer: await this.getTextColumn('customer', row),
      totalPaid: await this.getTextColumn('total_paid_tax_incl', row),
      payment: await this.getTextColumn('payment', row),
      status: await this.getTextColumn('osname', row),
    };
  }

  /**
   * Get order from table in csv format
   * @param row
   * @return {Promise<string>}
   */
  async getOrderInCsvFormat(row) {
    const order = await this.getOrderFromTable(row);
    return `${order.id};`
      + `${order.reference};`
      + `${order.newClient === 'Yes' ? 1 : 0};`
      + `${order.delivery.split(' ').length > 1 ? `"${order.delivery}";` : `${order.delivery};`}`
      + `"${order.customer}";`
      + `${order.totalPaid};`
      + `${order.payment.split(' ').length > 1 ? `"${order.payment}";` : `${order.payment};`}`
      + `${order.status.split(' ').length > 1 ? `"${order.status}";` : `${order.status};`}`;
  }
};
