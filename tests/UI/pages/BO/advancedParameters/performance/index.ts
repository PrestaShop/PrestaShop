import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Performance page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Performance extends BOBasePage {
  public readonly clearCacheSuccessMessage: string;

  public readonly pageTitle: string;

  private readonly clearCacheButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Performance page
   */
  constructor() {
    super();

    this.clearCacheSuccessMessage = 'All caches cleared successfully';

    this.pageTitle = 'Performance •';

    // Selectors
    this.clearCacheButton = '#page-header-desc-configuration-clear_cache';
  }

  /*
  Methods
   */
  /**
   * Clear cache
   * @param page{Page} Browser tab
   * @returns {Promise<string>}
   */
  async clearCache(page: Page): Promise<string> {
    await this.clickAndWaitForNavigation(page, this.clearCacheButton);

    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

export default new Performance();
