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

    // Text message
    this.messageSuccessSent = 'Message successfully sent';

    // Selectors
    this.ordersTable = '#content table';
    this.ordersTableRows = `${this.ordersTable} tbody tr`;
    this.ordersTableRow = row => `${this.ordersTableRows}:nth-child(${row})`;
    this.orderTableColumn = (row, column) => `${this.ordersTableRow(row)} td:nth-child(${column})`;
    this.reorderLink = row => `${this.ordersTableRow(row)} a.reorder-link`;
    this.detailsLink = row => `${this.ordersTableRow(row)} a.view-order-details-link`;
    this.orderTableColumnInvoice = row => `${this.orderTableColumn(row, 6)} a`;
    this.orderDetailsLink = orderID => `${this.ordersTableRows}`
      + ` td a.view-order-details-link[href$='order-detail&id_order=${orderID}']`;
    // Messages block
    this.boxMessagesSection = '.box.messages';
    this.messageRow = row => `${this.boxMessagesSection} div:nth-child(${row}).message.row`;
    // Add message block
    this.orderMessageForm = '.order-message-form';
    this.productSelect = `${this.orderMessageForm} select[data-role='product']`;
    this.messageTextarea = `${this.orderMessageForm} textarea[data-role='msg-text']`;
    this.sendMessageButton = `${this.orderMessageForm} button.form-control-submit`;
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
   * Is invoice visible on order history table row
   * @param page {Page} Browser tab
   * @param orderRow {number} Row number in orders table
   * @returns {Promise<boolean>}
   */
  isInvoiceVisible(page, orderRow = 1) {
    return this.elementVisible(page, this.orderTableColumnInvoice(orderRow), 1000);
  }

  /**
   * Get order id from invoice href
   * @param page {Page} Browser tab
   * @param orderRow {number} Row number in orders table
   * @returns {Promise<string>}
   */
  getOrderIdFromInvoiceHref(page, orderRow = 1) {
    return this.getAttributeContent(page, this.orderTableColumnInvoice(orderRow), 'href');
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

  /**
   * Go to order details page
   * @param page {Page} Browser tab
   * @param orderID {number} Order ID
   * @returns {Promise<void>}
   */
  async goToOrderDetailsPage(page, orderID = 1) {
    await this.clickAndWaitForNavigation(page, this.orderDetailsLink(orderID));
  }

  // Methods for box messages
  /**
   * Is box messages section visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isBoxMessagesSectionVisible(page) {
    return this.elementVisible(page, this.boxMessagesSection, 1000);
  }

  /**
   * Is message visible
   * @param page {Page} Browser tab
   * @param row {number} Row on table messages (2 is the first row)
   * @returns {Promise<boolean>}
   */
  isMessageRowVisible(page, row = 1) {
    return this.elementVisible(page, this.messageRow(row + 1), 1000);
  }

  /**
   * Get message row
   * @param page {Page} Browser tab
   * @param row {number} Row on table messages (2 is the first row)
   * @returns {Promise<string>}
   */
  getMessageRow(page, row = 1) {
    return this.getTextContent(page, this.messageRow(row + 1));
  }

  // Methods for Add message form
  /**
   * Send message
   * @param page {Page} Browser tab
   * @param messageText {{product: string, message:string}} Data to set on Add message form
   * @returns {Promise<string>}
   */
  async sendMessage(page, messageText) {
    if (messageText.product !== '') {
      await this.selectByVisibleText(page, this.productSelect, messageText.product);
    }

    await this.setValue(page, this.messageTextarea, messageText.message);
    await this.clickAndWaitForNavigation(page, this.sendMessageButton);

    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new OrderHistory();
