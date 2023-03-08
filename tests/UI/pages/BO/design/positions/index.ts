import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Positions page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Positions extends BOBasePage {
  public readonly pageTitle: string;

  private readonly searchInput: string;

  private readonly modulePositionForm: string;

  private readonly searchResultHookNameSpan: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on positions page
   */
  constructor() {
    super();

    this.pageTitle = `Module positions • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.searchInput = '#hook-search';
    this.modulePositionForm = '#module-positions-form';
    this.searchResultHookNameSpan = `${this.modulePositionForm} section[style] header span span`;
  }

  /* Methods */

  /**
   * Search for a hook
   * @param page {Page} Browser tab
   * @param hookValue {string} Value of hook to set on input
   * @returns {Promise<string>}
   */
  async searchHook(page: Page, hookValue: string): Promise<string> {
    await this.setValue(page, this.searchInput, hookValue);

    return this.getTextContent(page, this.searchResultHookNameSpan);
  }
}

export default new Positions();
