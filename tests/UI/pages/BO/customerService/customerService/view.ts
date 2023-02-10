import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';
import MessageData from '@data/faker/message';

/**
 * View customer service page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ViewCustomer extends BOBasePage {
  public readonly pageTitle: string;

  public readonly forwardMessageSuccessMessage: string;

  public readonly messageSuccessfullySend: string;

  private readonly threadBadge: string;

  private readonly messageInitialThreadDiv: string;

  private readonly messageThreadDiv: string;

  private readonly attachmentLink: string;

  private readonly statusButton: (statusID: number) => string;

  private readonly forwardMessageButton: string;

  private readonly yourAnswerFormTitle: string;

  private readonly yourAnswerFormTextarea: string;

  private readonly sendMessageButton: string;

  private readonly ordersAndMessagesBlock: string;

  private readonly forwardMessageModal: string;

  private readonly forwardModalEmployeeIDSelect: string;

  private readonly forwardModalCommentInput: string;

  private readonly forwardModalSendButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on view customer service page
   */
  constructor() {
    super();

    this.pageTitle = `View • ${global.INSTALL.SHOP_NAME}`;
    this.forwardMessageSuccessMessage = 'Message forwarded to';
    this.messageSuccessfullySend = 'The message was successfully sent to the customer.';

    // Selectors
    this.threadBadge = 'span.badge';
    this.messageInitialThreadDiv = '#content div.message-item-initial';
    this.attachmentLink = `${this.messageInitialThreadDiv} span.message-product a`;
    this.messageThreadDiv = '#content div[data-role="thread-messages"]';
    this.statusButton = (statusID: number) => `button[name='setstatus'][value='${statusID}']`;
    this.forwardMessageButton = 'button[data-target="#myModal"]';
    this.yourAnswerFormTitle = '#reply-form-title';
    this.yourAnswerFormTextarea = '#reply_message';
    this.sendMessageButton = '#content div.panel-footer button';
    this.ordersAndMessagesBlock = '#orders-and-messages-block';
    this.forwardMessageModal = '#myModal';
    this.forwardModalEmployeeIDSelect = `${this.forwardMessageModal} select[name='id_employee_forward']`;
    this.forwardModalCommentInput = `${this.forwardMessageModal} textarea[name='message_forward']`;
    this.forwardModalSendButton = `${this.forwardMessageModal} div.modal-footer button.btn.btn-primary`;
  }

  /*
  Methods
   */

  // Thread form
  /**
   * Get badge number
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getBadgeNumber(page: Page): Promise<string> {
    return this.getTextContent(page, this.threadBadge);
  }

  /**
   * Get customer message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getCustomerMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.messageInitialThreadDiv);
  }

  /**
   * Get thread messages
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getThreadMessages(page: Page): Promise<string> {
    return this.getTextContent(page, this.messageThreadDiv);
  }

  /**
   * Get attached href
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getAttachedFileHref(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.attachmentLink, 'href');
  }

  /**
   * Set status
   * @param page {Page} Browser tab
   * @param status {string} Status to set on the message
   * @returns {Promise<string>}
   */
  async setStatus(page: Page, status: string): Promise<string> {
    let statusID: number = 0;

    switch (status) {
      case 'Re-open':
        statusID = 1;
        break;

      case 'Handled':
        statusID = 2;
        break;

      case 'Pending 1':
        statusID = 3;
        break;

      case 'Pending 2':
        statusID = 4;
        break;

      default:
        throw new Error(`Status ${status} was not found`);
    }

    await this.waitForSelectorAndClick(page, this.statusButton(statusID));

    if (status === 'Re-open') {
      return this.getTextContent(page, this.statusButton(2));
    }
    return this.getTextContent(page, this.statusButton(1));
  }

  /**
   * Click on forward message button
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnForwardMessageButton(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.forwardMessageButton);

    return this.elementVisible(page, this.forwardMessageModal, 2000);
  }

  /**
   * Forward message
   * @param page {Page} Browser tab
   * @param messageData {MessageData} Message data to set
   * @returns {Promise<void>}
   */
  async forwardMessage(page: Page, messageData: MessageData): Promise<void> {
    await this.selectByVisibleText(page, this.forwardModalEmployeeIDSelect, messageData.employeeName);
    await this.setValue(page, this.forwardModalCommentInput, messageData.message);

    await this.waitForSelectorAndClick(page, this.forwardModalSendButton);
  }

  // Your answer form
  /**
   * Get your answer form title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getYourAnswerFormTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.yourAnswerFormTitle);
  }

  /**
   * Get your answer form content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getYourAnswerFormContent(page: Page): Promise<string> {
    return this.getTextContent(page, this.yourAnswerFormTextarea);
  }

  /**
   * Add response to customer
   * @param page {Page} Browser tab
   * @param response {string} response to set to the customer
   * @returns {Promise<void>}
   */
  async addResponse(page: Page, response: string): Promise<void> {
    await this.setValue(page, this.yourAnswerFormTextarea, response);
    await this.waitForSelectorAndClick(page, this.sendMessageButton);
  }

  // Orders and messages timeline form
  /**
   * Get orders and messages form content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getOrdersAndMessagesTimeline(page: Page): Promise<string> {
    return this.getTextContent(page, this.ordersAndMessagesBlock);
  }
}

export default new ViewCustomer();
