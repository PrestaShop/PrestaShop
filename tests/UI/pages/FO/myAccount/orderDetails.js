require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class OrderHistory extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Order details';
    this.successMessageText = 'Message successfully sent';

    // Selectors
    this.orderReturnForm = '#order-return-form';
    this.productIdSelect = '[name=id_product]';
    this.messageTextarea = '[name=msgText]';
    this.submitMessageButton = '[name=submitMessage]';
    this.headerTitle = '.page-header h1';
    this.reorderLink = '#order-infos a';

    // Table selectors
    this.gridTable = '#order-products';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = row => `${this.tableBodyRow(row)} td`;

    // Table content
    this.productName = row => `${this.tableBodyColumn(row)} a`;
  }

  /*
  Methods
   */

  /**
   * Is orderReturn form visible
   * @param page {Page} Browser tab
   * @returns {boolean}
   */
  isOrderReturnFormVisible(page) {
    return this.elementVisible(page, this.orderReturnForm, 1000);
  }

  /**
   * Add a message to order history
   * @param page {Page} Browser tab
   * @param messageOption {String} The reference of the order
   * @param messageText {String} The message content
   * @returns {Promise<string>}
   */
  async addAMessage(page, messageOption, messageText) {
    await this.selectByVisibleText(page, this.productIdSelect, messageOption);
    await this.setValue(page, this.messageTextarea, messageText);
    await this.clickAndWaitForNavigation(page, this.submitMessageButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Retrieve and return product name from order detail page
   * @param page {Page} Browser tab
   * @param row {Number} row in orders details table
   * @returns {Promise<string>}
   */
  getProductName(page, row = 1) {
    return this.getTextContent(page, this.productName(row));
  }

  /**
   * @override
   * Get the page title from the main section
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPageTitle(page) {
    return this.getTextContent(page, this.headerTitle);
  }

  async clickOnReorderLink(page) {
    await this.clickAndWaitForNavigation(page, this.reorderLink);
  }
}

module.exports = new OrderHistory();
