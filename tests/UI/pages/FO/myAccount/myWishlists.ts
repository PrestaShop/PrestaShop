import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * Vouchers page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class MyWishlists extends FOBasePage {
  public readonly pageTitle: string;

  private readonly headerTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on vouchers page
   */
  constructor() {
    super();

    this.pageTitle = 'My wishlists';

    // Selectors
    this.headerTitle = '#content-wrapper h1';
  }

  /*
  Methods
   */
  /**
   * @override
   * Get the page title from the main section
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPageTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.headerTitle);
  }
}

export default new MyWishlists();
