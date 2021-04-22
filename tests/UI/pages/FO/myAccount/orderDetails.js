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
   * @param messageOption {string} String for the message option
   * @param messageText {string} String for the message content
   * @returns {Promise<string>}
   */
  async addAMessage(page, messageOption, messageText) {
    await this.selectByVisibleText(page, this.productIdSelect, messageOption);
    await this.setValue(page, this.messageTextarea, messageText);
    await this.clickAndWaitForNavigation(page, this.submitMessageButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new OrderHistory();
