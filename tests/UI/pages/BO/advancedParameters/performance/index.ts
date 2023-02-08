import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Performance page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Performance extends BOBasePage {
  public readonly clearCacheSuccessMessage: string;

  public readonly successUpdateMessage: string;

  public readonly pageTitle: string;

  private readonly clearCacheButton: string;

  private readonly saveDebugModeForm: string;

  private readonly debugModeButton: (toEnable: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Performance page
   */
  constructor() {
    super();

    this.clearCacheSuccessMessage = 'All caches cleared successfully';
    this.successUpdateMessage = 'Update successful';

    this.pageTitle = 'Performance •';

    // Selectors
    this.clearCacheButton = '#page-header-desc-configuration-clear_cache';
    this.debugModeButton = (toEnable: number) => `#debug_mode_debug_mode_${toEnable}`;
    this.saveDebugModeForm = '#main-div form[name=debug_mode] div.card-footer button';
  }

  /*
  Methods
   */
  /**
   * Clear cache
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async clearCache(page: Page): Promise<string> {
    await this.clickAndWaitForLoadState(page, this.clearCacheButton);

    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Set debug mode
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable debug mode
   * @returns {Promise<string>}
   */
  async setDebugMode(page: Page, toEnable: boolean): Promise<string> {
    await this.setChecked(page, this.debugModeButton(toEnable ? 1 : 0));
    await this.waitForSelectorAndClick(page, this.saveDebugModeForm);

    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Is debug mode toggle visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isDebugModeToggleVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.debugModeToolbar, 1000);
  }
}

export default new Performance();
