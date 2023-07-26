// Import pages
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import AuthorizedApplicationData from '@data/faker/authorizedApplication';

import type {Page} from 'playwright';

/**
 * New authorized application page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ViewAuthorizedApp extends BOBasePage {
  public readonly pageTitle: (appName: string) => string;

  private readonly appInformationCard: string;

  private readonly appInformationCardAppName: string;

  private readonly appInformationCardDescription: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Performance page
   */
  constructor() {
    super();

    this.pageTitle = (appName: string) => `${appName} • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.appInformationCard = 'div.view-authorized-application-card div.card-body ';
    this.appInformationCardAppName = `${this.appInformationCard} div:nth-child(1) div.col-8`;
    this.appInformationCardDescription = `${this.appInformationCard} div:nth-child(2) div.col-8`;
  }

  /*
  Methods
   */

  /**
   * Returns application informations
   * @param page {Page}
   * @returns Promise<AuthorizedApplicationData>
   */
  async getAppInformation(page: Page): Promise<AuthorizedApplicationData> {
    return new AuthorizedApplicationData({
      appName: await this.getTextContent(page, this.appInformationCardAppName),
      description: await this.getTextContent(page, this.appInformationCardDescription),
    });
  }
}

export default new ViewAuthorizedApp();
