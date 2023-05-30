import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

import {OrderHistory, OrderHistoryMessage} from '@data/types/order';

/**
 * Order history page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class OrderHistoryPage extends FOBasePage {
  public readonly pageTitle: string;

  public readonly messageSuccessSent: string;

  private readonly ordersTable: string;

  private readonly ordersTableRows: string;

  private readonly ordersTableRow: (row: number) => string;

  private readonly orderTableColumn: (row: number, column: number) => string;

  private readonly orderTableColumnReference: (row: number) => string;

  private readonly reorderLink: (row: number) => string;

  private readonly detailsLink: (row: number) => string;

  private readonly orderTableColumnInvoice: (row: number) => string;

  private readonly orderDetailsLink: (orderID: number) => string;

  private readonly backToYourAccountLink: string;

  private readonly homeLink: string;

  private readonly boxMessagesSection: string;

  private readonly messageRow: (row: number) => string;

  private readonly orderMessageForm: string;

  private readonly productSelect: string;

  private readonly messageTextarea: string;

  private readonly sendMessageButton: string;

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
    this.ordersTableRow = (row: number) => `${this.ordersTableRows}:nth-child(${row})`;
    this.orderTableColumn = (row: number, column: number) => `${this.ordersTableRow(row)} td:nth-child(${column})`;
    this.orderTableColumnReference = (row: number) => `${this.ordersTableRow(row)} th:nth-child(1)`;
    this.reorderLink = (row: number) => `${this.ordersTableRow(row)} a.reorder-link`;
    this.detailsLink = (row: number) => `${this.ordersTableRow(row)} a.view-order-details-link`;
    this.orderTableColumnInvoice = (row: number) => `${this.ordersTableRow(row)} td:nth-child(6) a`;
    this.orderDetailsLink = (orderID: number) => `${this.ordersTableRows}`
      + ` td a.view-order-details-link[href$='order-detail&id_order=${orderID}']`;
    this.backToYourAccountLink = 'footer  a[data-role=back-to-your-account]';
    this.homeLink = 'footer  a[data-role=home]';

    // Messages block
    this.boxMessagesSection = '.box.messages';
    this.messageRow = (row: number) => `${this.boxMessagesSection} div:nth-child(${row}).message.row`;

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
  async getNumberOfOrders(page: Page): Promise<number> {
    return (await page.$$(this.ordersTableRows)).length;
  }

  /**
   * Get order history details
   * @param page {Page} Browser tab
   * @param row {number} Row in order history table
   */
  async getOrderHistoryDetails(page: Page, row: number = 1): Promise<OrderHistory> {
    return {
      reference: await this.getTextContent(page, this.orderTableColumnReference(row)),
      date: await this.getTextContent(page, this.orderTableColumn(row, 2)),
      price: await this.getTextContent(page, this.orderTableColumn(row, 3)),
      paymentType: await this.getTextContent(page, this.orderTableColumn(row, 4)),
      status: await this.getTextContent(page, this.orderTableColumn(row, 5)),
      invoice: await this.getTextContent(page, this.orderTableColumn(row, 6)),
    };
  }

  /**
   * Is reorder link visible
   * @param page {Page} Browser tab
   * @param orderRow {Number} Row on orders table
   * @returns {Promise<boolean>}
   */
  isReorderLinkVisible(page: Page, orderRow: number = 1): Promise<boolean> {
    return this.elementVisible(page, this.reorderLink(orderRow), 1000);
  }

  /**
   *
   * Click on reorder link
   * @param page {Page} Browser tab
   * @param orderRow {Number} Row in orders table
   * @returns {Promise<void>}
   */
  async clickOnReorderLink(page: Page, orderRow: number = 1): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.reorderLink(orderRow));
  }

  /**
   * Get order status from orders history page
   * @param page {Page} Browser tab
   * @param orderRow {number} Row number in orders table
   * @return {Promise<string>}
   */
  getOrderStatus(page: Page, orderRow: number = 1): Promise<string> {
    return this.getTextContent(page, `${this.orderTableColumn(orderRow, 5)} span`);
  }

  /**
   * Is invoice visible on order history table row
   * @param page {Page} Browser tab
   * @param orderRow {number} Row number in orders table
   * @returns {Promise<boolean>}
   */
  isInvoiceVisible(page: Page, orderRow: number = 1): Promise<boolean> {
    return this.elementVisible(page, this.orderTableColumnInvoice(orderRow), 1000);
  }

  /**
   * Get order id from invoice href
   * @param page {Page} Browser tab
   * @param orderRow {number} Row number in orders table
   * @returns {Promise<string>}
   */
  getOrderIdFromInvoiceHref(page: Page, orderRow: number = 1): Promise<string> {
    return this.getAttributeContent(page, this.orderTableColumnInvoice(orderRow), 'href');
  }

  /**
   * Download invoice
   * @param page {Page} Browser tab
   * @param row {number} Row number in orders table
   */
  async downloadInvoice(page: Page, row: number = 1): Promise<string | null> {
    return this.clickAndWaitForDownload(page, this.orderTableColumnInvoice(row));
  }

  /**
   * Go to details page from order history page
   * @param page {Page} Browser tab
   * @param orderRow {Number} row in orders table
   * @returns {Promise<void>}
   */
  async goToDetailsPage(page: Page, orderRow: number = 1): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.detailsLink(orderRow));
  }

  /**
   * Go to order details page
   * @param page {Page} Browser tab
   * @param orderID {number} Order ID
   * @returns {Promise<void>}
   */
  async goToOrderDetailsPage(page: Page, orderID: number = 1): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.orderDetailsLink(orderID));
  }

  /**
   * Click on back to your account link
   * @param page {Page} Browser tab
   */
  async clickOnBackToYourAccountLink(page: Page): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.backToYourAccountLink);
  }

  /**
   * Click on home link
   * @param page {Page} Browser tab
   */
  async clickOnHomeLink(page: Page): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.homeLink);
  }

  // Methods for box messages
  /**
   * Is box messages section visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isBoxMessagesSectionVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.boxMessagesSection, 1000);
  }

  /**
   * Is message visible
   * @param page {Page} Browser tab
   * @param row {number} Row on table messages (2 is the first row)
   * @returns {Promise<boolean>}
   */
  isMessageRowVisible(page: Page, row: number = 1): Promise<boolean> {
    return this.elementVisible(page, this.messageRow(row + 1), 1000);
  }

  /**
   * Get message row
   * @param page {Page} Browser tab
   * @param row {number} Row on table messages (2 is the first row)
   * @returns {Promise<string>}
   */
  getMessageRow(page: Page, row: number = 1): Promise<string> {
    return this.getTextContent(page, this.messageRow(row + 1));
  }

  // Methods for Add message form
  /**
   * Send message
   * @param page {Page} Browser tab
   * @param messageText {OrderHistoryMessage} Data to set on Add message form
   * @returns {Promise<string>}
   */
  async sendMessage(page: Page, messageText: OrderHistoryMessage): Promise<string> {
    if (messageText.product !== '') {
      await this.selectByVisibleText(page, this.productSelect, messageText.product);
    }

    await this.setValue(page, this.messageTextarea, messageText.message);
    await this.clickAndWaitForNavigation(page, this.sendMessageButton);

    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

export default new OrderHistoryPage();
