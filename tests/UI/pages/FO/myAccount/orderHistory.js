require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Order history page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class OrderHistory extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on order history page
   */
  constructor() {
    super();

    this.pageTitle = 'Order history';

    // Selectors
    this.ordersTable = '#content table';
    this.ordersTableRows = `${this.ordersTable} tbody tr`;
    this.ordersTableRow = row => `${this.ordersTableRows}:nth-child(${row})`;
    this.orderTableColumn = (row, column) => `${this.ordersTableRow(row)} td:nth-child(${column})`;
    this.reorderLink = row => `${this.ordersTableRow(row)} a.reorder-link`;
    this.detailsLink = row => `${this.ordersTableRow(row)} a.view-order-details-link`;
  }

  /*
  Methods
   */

  /**
   * Get number of order in order history page
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfOrders(page) {
    return (await page.$$(this.ordersTableRows)).length;
  }

  /**
   * Is reorder link visible
   * @param page {Page} Browser tab
   * @param orderRow {Number} Row on orders table
   * @returns {Promise<boolean>}
   */
  isReorderLinkVisible(page, orderRow = 1) {
    return this.elementVisible(page, this.reorderLink(orderRow), 1000);
  }

  /**
   *
   * Click on reorder link
   * @param page {Page} Browser tab
   * @param orderRow {Number} Row in orders table
   * @returns {Promise<void>}
   */
  async clickOnReorderLink(page, orderRow = 1) {
    await this.clickAndWaitForNavigation(page, this.reorderLink(orderRow));
  }

  /**
   * Get order status from orders history page
   * @param page {Page} Browser tab
   * @param orderRow {number} Row number in orders table
   * @return {Promise<string>}
   */
  getOrderStatus(page, orderRow = 1) {
    return this.getTextContent(page, `${this.orderTableColumn(orderRow, 5)} span`);
  }

  /**
   * Go to details page from order history page
   * @param page {Page} Browser tab
   * @param orderRow {Number} row in orders table
   * @returns {Promise<void>}
   */
  async goToDetailsPage(page, orderRow = 1) {
    await this.clickAndWaitForNavigation(page, this.detailsLink(orderRow));
  }
}

module.exports = new OrderHistory();
