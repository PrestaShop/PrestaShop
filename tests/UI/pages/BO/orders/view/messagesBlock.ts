import {ViewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';

import type {Page} from 'playwright';
import {OrderMessage} from '@data/types/order';

/**
 * Messages block, contains functions that can be used on view/edit messages block on view order page
 * @class
 * @extends ViewOrderBasePage
 */
class MessagesBlock extends ViewOrderBasePage {
  private readonly messageBlock: string;

  private readonly messageBlockTitle: string;

  private readonly orderMessageSelect: string;

  private readonly displayToCustometCheckbox: string;

  private readonly messageTextarea: string;

  private readonly sendMessageButton: string;

  private readonly messageBlockList: string;

  private readonly messageListChild: (messageID: number) => string;

  private readonly messageBlockEmployee: (messageID: number) => string;

  private readonly messageBlockCustomer: (messageID: number) => string;

  private readonly messageEmployeeBlockContent: (messageID: number) => string;

  private readonly messageCustomerBlockContent: (messageID: number) => string;

  private readonly messageBlockIcon: (messageID: number) => string;

  private readonly configureLink: string;

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
    this.messageListChild = (messageID: number) => `${this.messageBlockList} li:nth-child(${messageID})`;
    this.messageBlockEmployee = (messageID: number) => `${this.messageListChild(messageID)}.messages-block-employee`;
    this.messageBlockCustomer = (messageID: number) => `${this.messageListChild(messageID)}.messages-block-customer`;
    this.messageEmployeeBlockContent = (messageID: number) => `${this.messageBlockEmployee(messageID)} .messages-block-content`;
    this.messageCustomerBlockContent = (messageID: number) => `${this.messageBlockCustomer(messageID)} .messages-block-content`;
    this.messageBlockIcon = (messageID: number) => `${this.messageBlockEmployee(messageID)} .messages-block-icon`;
    this.configureLink = `${this.messageBlock} .configure-link`;
  }

  /*
  Methods
   */
  /**
   * Send message
   * @param page {Page} Browser tab
   * @param messageData {OrderMessage} Data to set on the form
   * @returns {Promise<string>}
   */
  async sendMessage(page: Page, messageData: OrderMessage): Promise<string> {
    await this.selectByVisibleText(page, this.orderMessageSelect, messageData.orderMessage);
    await this.setChecked(page, this.displayToCustometCheckbox, messageData.displayToCustomer);

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
  getMessagesNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.messageBlockTitle);
  }

  /**
   * Is message visible
   * @param page {Page} Browser tab
   * @param messageID {number} Message ID on the list
   * @param messageFrom {string} The message sender
   * @returns {Promise<boolean>}
   */
  isMessageVisible(page: Page, messageID: number = 1, messageFrom: string = 'employee'): Promise<boolean> {
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
  isEmployeeIconVisible(page: Page, messageID: number = 1): Promise<boolean> {
    return this.elementVisible(page, `${this.messageBlockIcon(messageID)} .employee-icon`, 1000);
  }

  /**
   * Is employee private icon visible
   * @param page {Page} Browser tab
   * @param messageID {number} Message id number
   * @returns {Promise<boolean>}
   */
  isEmployeePrivateIconVisible(page: Page, messageID: number = 1): Promise<boolean> {
    return this.elementVisible(page, `${this.messageBlockIcon(messageID)} .employee-icon--private`, 1000);
  }

  /**
   * Get text message
   * @param page {Page} Browser tab
   * @param messageID {number} Message ID on the list
   * @param messageFrom {string} The message sender
   * @returns {Promise<string>}
   */
  getTextMessage(page: Page, messageID: number = 1, messageFrom: string = 'employee'): Promise<string> {
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
  async clickOnConfigureMessageLink(page: Page): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.configureLink);
  }
}

export default new MessagesBlock();
