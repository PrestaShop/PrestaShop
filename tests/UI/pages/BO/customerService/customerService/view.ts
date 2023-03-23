import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * View customer service page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ViewCustomer extends BOBasePage {
  public readonly pageTitle: (threadNumber: string) => string;

  private readonly threadBadge: string;

  private readonly messagesThredDiv: string;

  private readonly attachmentLink: string;

  private readonly statusButton: (statusID: number) => string;

  private readonly yourAnswerFormTitle: string;

  private readonly yourAnswerFormTextarea: string;

  private readonly ordersAndMessagesBlock: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on view customer service page
   */
  constructor() {
    super();

    this.pageTitle = (threadNumber: string) => `Customer thread #${threadNumber} • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.threadBadge = '#main-div div[data-role="messages-thread"] .card-header';
    this.messagesThredDiv = '#main-div div[data-role="messages-thread"]';
    this.attachmentLink = `${this.messagesThredDiv} a[href*='/upload']`;
    this.statusButton = (statusName: string) => `${this.messagesThredDiv} form input[value='${statusName}'] + button`;
    this.yourAnswerFormTitle = '#main-div div[data-role="employee-answer"] h3.card-header';
    this.yourAnswerFormTextarea = '#reply_to_customer_thread_reply_message';
    this.ordersAndMessagesBlock = '#main-div div[data-role="messages_timeline"]';
  }

  /*
  Methods
   */

  // Thread form
  /**
   * Get thread number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getThreadNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.threadBadge);
  }

  /**
   * Get customer message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getCustomerMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.messagesThredDiv);
  }

  /**
   * Get attached href
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  getAttachedFileHref(page: Page): Promise<string | null> {
    return this.getAttributeContent(page, this.attachmentLink, 'href');
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

  // Orders and messages timeline form
  /**
   * Get orders and messages form content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getOrdersAndMessagesTimeline(page: Page): Promise<string> {
    return this.getTextContent(page, this.ordersAndMessagesBlock);
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
}

export default new ViewCustomer();
