require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Order extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Orders •';
    this.orderPageTitle = 'Order';

    // Orders page
    this.ordersForm = '#form-order';
    this.ordersNumberSpan = `${this.ordersForm} span.badge`;
    this.ordersTable = '#table-order';
    this.orderFilterColumnInput = `${this.ordersTable} input[name='orderFilter_%FILTERBY']`;
    this.orderFilterColumnSelect = `${this.ordersTable} select[name='orderFilter_os!id_%FILTERBY']`;
    this.searchButton = `${this.ordersTable}  #submitFilterButtonorder`;
    this.resetButton = `${this.ordersTable} button.btn.btn-warning`;
    this.orderRow = `${this.ordersTable} tbody tr:nth-child(%ROW)`;
    this.orderfirstLineIdTD = `${this.orderRow} td:nth-child(2)`;
    this.orderfirstLineReferenceTD = `${this.orderRow} td:nth-child(3)`;
    this.orderfirstLineStatusTD = `${this.orderRow} td:nth-child(9)`;
  }

  /*
  Methods
   */
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
        await this.setValue(this.orderFilterColumnInput.replace('%FILTERBY', filterBy), value);
        // click on search
        await Promise.all([
          this.page.waitForNavigation({waitUntil: 'networkidle0'}),
          this.page.click(this.searchButton),
        ]);
        break;
      case 'select':
        await Promise.all([
          this.page.waitForNavigation({waitUntil: 'networkidle0'}),
          this.selectByVisibleText(this.orderFilterColumnSelect.replace('%FILTERBY', filterBy), value),
        ]);
        break;
      default:
      // Do nothing
    }
  }

  /**
   * Reset filter in orders
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (await this.elementVisible(this.resetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.resetButton);
    }
    return this.getNumberFromText(this.ordersNumberSpan);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberFromText(this.ordersNumberSpan);
  }

  /**
   * Go to orders Page
   * @param orderRow
   * @return {Promise<void>}
   */
  async goToOrder(orderRow) {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.orderfirstLineIdTD.replace('%ROW', orderRow)),
    ]);
  }
};
