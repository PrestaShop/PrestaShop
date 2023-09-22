import BOBasePage from '@pages/BO/BObasePage';
import {Page} from 'playwright';

/**
 * Dashboard page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Dashboard extends BOBasePage {
  public readonly pageTitle: string;

  private readonly demoModeButton: string;

  private readonly demoModeToggle: (toEnable: string) => string;

  private readonly salesScore: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on dashboard page
   */
  constructor() {
    super();

    this.pageTitle = `Dashboard • ${global.INSTALL.SHOP_NAME}`;

    // selectors
    this.demoModeButton = '#page-header-desc-configuration-switch_demo';
    this.demoModeToggle = (toEnable: string) => `.process-icon-toggle-${toEnable}.switch_demo`;
    this.salesScore = '#sales_score';
  }

  /* Methods */

  /**
   * Set demo mode
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable demo mode, false if not
   */
  async setDemoMode(page: Page, toEnable: boolean): Promise<void> {
    const isDemoModeOn = await this.elementVisible(page, this.demoModeToggle('on'), 2000);

    if ((toEnable && !isDemoModeOn) || (!toEnable && isDemoModeOn)) {
      await this.waitForSelectorAndClick(page, this.demoModeButton);
      await page.waitForTimeout(2000);
    }
  }

  /**
   * Get sales score
   * @param page {Page} Browser tab
   */
  async getSalesScore(page: Page): Promise<number> {
    const text = await this.getTextContent(page, this.salesScore);

    if (text === null) {
      return 0;
    }
    const regexMatch: RegExpMatchArray|null = text.match(/\d+(\.\d+)?/g);

    if (regexMatch === null) {
      return 0;
    }
    const salesScore: string = regexMatch.toString().replace(',', '');

    return parseFloat(salesScore);
  }
}

export default new Dashboard();
