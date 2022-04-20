require('module-alias/register');
const ViewOrderBasePage = require('@pages/BO/orders/view/viewOrderBasePage');

/**
 * Messages block, contains functions that can be used on view/edit messages block on view order page
 * @class
 * @extends ViewOrderBasePage
 */
class MessagesBlock extends ViewOrderBasePage.constructor {
  /**
   * @constructs
   * Setting up texts and selectors to use on view/edit messages block
   */
  constructor() {
    super();

    // Messages block
    this.messageBlock = 'div[data-role=\'message-card\']';
    this.messageBlockTitle = `${this.messageBlock} .card-header-title`;
    this.orderMessageSelect = '#order_message_order_message';
    this.displayToCustometCheckbox = `${this.messageBlock} .md-checkbox label`;
    this.messageTextarea = '#order_message_message';
    this.sendMessageButton = `${this.messageBlock} .btn-primary`;
    this.messageBlockList = `${this.messageBlock} .messages-block`;
    this.messageListChild = messageID => `${this.messageBlockList} li:nth-child(${messageID})`;
    this.messageBlockEmployee = messageID => `${this.messageListChild(messageID)}.messages-block-employee`;
    this.messageBlockCustomer = messageID => `${this.messageListChild(messageID)}.messages-block-customer`;
    this.messageEmployeeBlockContent = messageID => `${this.messageBlockEmployee(messageID)} .messages-block-content`;
    this.messageCustomerBlockContent = messageID => `${this.messageBlockCustomer(messageID)} .messages-block-content`;
    this.messageBlockIcon = messageID => `${this.messageBlockEmployee(messageID)} .messages-block-icon`;
    this.configureLink = `${this.messageBlock} .configure-link`;
  }

  /*
  Methods
   */
  /**
   * Send message
   * @param page {Page} Browser tab
   * @param messageData {{orderMessage: string, displayToCustomer : boolean, message : string}} Data to set on the form
   * @returns {Promise<string>}
   */
  async sendMessage(page, messageData) {
    await this.selectByVisibleText(page, this.orderMessageSelect, messageData.orderMessage);
    if (messageData.displayToCustomer) {
      await this.setChecked(page, this.displayToCustometCheckbox, messageData.displayToCustomer);
    }

    if (messageData.message !== '') {
      await this.setValue(page, this.messageTextarea, messageData.message);
    }

    await this.waitForSelectorAndClick(page, this.sendMessageButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get messages number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getMessagesNumber(page) {
    return this.getNumberFromText(page, this.messageBlockTitle);
  }

  /**
   * Is message visible
   * @param page {Page} Browser tab
   * @param messageID {number} Message ID on the list
   * @param messageFrom {string} The message sender
   * @returns {Promise<boolean>}
   */
  isMessageVisible(page, messageID = 1, messageFrom = 'employee') {
    if (messageFrom === 'employee') {
      return this.elementVisible(page, this.messageEmployeeBlockContent(messageID), 1000);
    }

    return this.elementVisible(page, this.messageCustomerBlockContent(messageID), 1000);
  }

  /**
   * Is employee icon visible
   * @param page {Page} Browser tab
   * @param messageID {number} Message id number
   * @returns {Promise<boolean>}
   */
  isEmployeeIconVisible(page, messageID = 1) {
    return this.elementVisible(page, `${this.messageBlockIcon(messageID)} .employee-icon`, 1000);
  }

  /**
   * Is employee private icon visible
   * @param page {Page} Browser tab
   * @param messageID {number} Message id number
   * @returns {Promise<boolean>}
   */
  isEmployeePrivateIconVisible(page, messageID = 1) {
    return this.elementVisible(page, `${this.messageBlockIcon(messageID)} .employee-icon--private`, 1000);
  }

  /**
   * Get text message
   * @param page {Page} Browser tab
   * @param messageID {number} Message ID on the list
   * @param messageFrom {string} The message sender
   * @returns {Promise<string>}
   */
  getTextMessage(page, messageID = 1, messageFrom = 'employee') {
    if (messageFrom === 'employee') {
      return this.getTextContent(page, this.messageEmployeeBlockContent(messageID));
    }

    return this.getTextContent(page, this.messageCustomerBlockContent(messageID));
  }

  /**
   * Click on configure predefined messages link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnConfigureMessageLink(page) {
    await this.clickAndWaitForNavigation(page, this.configureLink);
  }
}

module.exports = new MessagesBlock();
