require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class OrderHistory extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Order details';
    this.successMessageText = 'Message successfully sent';
    this.messageSend = 'Test';

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
   * @param page
   * @returns {boolean}
   */
  isOrderReturnFormVisible(page) {
    return this.elementVisible(page, this.orderReturnForm, 1000);
  }

  async addAMessage(page, messageOption) {
    await this.selectByVisibleText(page, this.productIdSelect, messageOption);
    await this.setValue(page, this.messageTextarea, this.messageSend);
    await this.clickAndWaitForNavigation(page, this.submitMessageButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new OrderHistory();
