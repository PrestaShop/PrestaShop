import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';
import {Page} from 'playwright';

/**
 * Module configuration page for module : psgdpr, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsGdprPage extends ModuleConfiguration {
  public readonly pageSubTitle: string;

  private readonly menuTab: (nth: number) => string;

  private readonly tabDataConsent: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on GDPR module configuration page
   */
  constructor() {
    super();

    this.pageSubTitle = 'Official GDPR compliance';

    this.menuTab = (nth: number) => `#psgdpr-menu .list-group:nth-child(1) a.list-group-item:nth-child(${nth})`;

    this.tabDataConsent = '#dataConsent .panel';
  }

  /**
   * Click on a specific tab and return if the tab is visible
   * @param page {Page} Browser tab
   * @param nth {number} Tab
   * @returns {Promise<boolean>}
   */
  async goToTab(page: Page, nth: number): Promise<boolean> {
    await this.clickAndWaitForLoadState(page, this.menuTab(nth));

    let selectorBlock: string;

    switch (nth) {
      case 3:
        selectorBlock = this.tabDataConsent;
        break;
      default:
        throw new Error(`The block #${nth} has not defined a defined selector.`);
    }

    return this.elementVisible(page, selectorBlock, 3000);
  }
}

export default new PsGdprPage();
