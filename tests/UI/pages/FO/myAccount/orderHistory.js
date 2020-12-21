require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class OrderHistory extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Order history';

    // Selectors
    this.ordersTable = '#content table';
    this.ordersTableRow = row => `${this.ordersTable} tbody tr:nth-child(${row})`;
    this.orderTableColumn = (row, column) => `${this.ordersTableRow(row)} td:nth-child(${column})`;
    this.reorderLink = id => `${this.ordersTable} td.order-actions a[href*='Reorder=&id_order=${id}']`;
    this.detailsLink = row => `${this.ordersTableRow(row)} a[data-link-action='view-order-details']`;
  }

  /*
  Methods
   */

  /**
   * Is reorder link visible
   * @param page
   * @param idOrder, database id of the order
   * @returns {boolean}
   */
  isReorderLinkVisible(page, idOrder = 1) {
    return this.elementVisible(page, this.reorderLink(idOrder), 1000);
  }

  /**
   * Get order status from orders history page
   * @param page
   * @param orderRow, row in orders table
   * @return {Promise<string>}
   */
  getOrderStatus(page, orderRow = 1) {
    return this.getTextContent(page, `${this.orderTableColumn(orderRow, 5)} span`);
  }

  /**
   * Go to details page
   * @param page
   * @param orderRow
   * @returns {Promise<void>}
   */
  async goToDetailsPage(page, orderRow = 1) {
    await this.clickAndWaitForNavigation(page, this.detailsLink(orderRow));
  }
}

module.exports = new OrderHistory();
