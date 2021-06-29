require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Order details page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class OrderDetails extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on order details page
   */
  constructor() {
    super();

    this.pageTitle = 'Order details';
    this.successMessageText = 'Message successfully sent';

    // Selectors
    this.headerTitle = '.page-header h1';
    this.reorderLink = '#order-infos a';

    // Order return form selectors
    this.orderReturnForm = '#order-return-form';
    this.gridTable = '#order-products';
    this.returnTextarea = `${this.orderReturnForm} textarea[name='returnText']`;
    this.requestReturnButton = `${this.orderReturnForm} button[name='submitReturnMerchandise']`;

    // Order products table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row, column) => `${this.tableBodyRow(row)} td:nth-child(${column})`;

    // Order product table content
    this.productName = (row, column) => `${this.tableBodyColumn(row, column)} a`;

    // Add message form selectors
    this.productIdSelect = '[name=id_product]';
    this.messageTextarea = '[name=msgText]';
    this.submitMessageButton = '[name=submitMessage]';
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
   * Request merchandise return
   * @param page {Page} Browser tab
   * @param messageText {string} Value of message text to set on return input
   * @returns {Promise<void>}
   */
  async requestMerchandiseReturn(page, messageText) {
    await page.check(`${this.tableBodyColumn(1, 1)} input`);
    await this.setValue(page, this.returnTextarea, messageText);
    await this.clickAndWaitForNavigation(page, this.requestReturnButton);
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
   * @param column {Number} column in orders details table
   * @returns {Promise<string>}
   */
  getProductName(page, row = 1, column = 1) {
    return this.getTextContent(page, this.productName(row, column));
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

  /**
   * Click on the reorder link in the order detail
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnReorderLink(page) {
    await this.clickAndWaitForNavigation(page, this.reorderLink);
  }
}

module.exports = new OrderDetails();
