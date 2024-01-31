import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * Return details page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class ReturnDetails extends FOBasePage {
  public readonly pageTitle: string;

  public readonly errorMessage: string;

  public readonly orderReturnCardBlock: string;

  private readonly pageTitleHeader: string;

  private readonly alertWarning: string;

  private readonly orderReturnInfo: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on return details page
   */
  constructor() {
    super();

    this.pageTitle = 'Return details';
    this.errorMessage = 'You must wait for confirmation before returning any merchandise.';
    this.orderReturnCardBlock = 'We have logged your return request. Your package must be returned to us within 14 days'
      + ' of receiving your order. The current status of your merchandise return is:';

    // Selectors
    this.pageTitleHeader = '#main header h1.h1';
    this.alertWarning = '#notifications .notifications-container article.alert-warning';
    this.orderReturnInfo = '#order-return-infos';
  }

  /*
  Methods
   */

  /**
   * Get page title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPageTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.pageTitleHeader);
  }

  /**
   * get return notifications
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getAlertWarning(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertWarning);
  }

  /**
   * Is alert warning visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isAlertWarningVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.alertWarning);
  }

  /**
   * Get order return info
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderReturnInfo(page: Page): Promise<string> {
    return this.getTextContent(page, this.orderReturnInfo);
  }
}

export default new ReturnDetails();
