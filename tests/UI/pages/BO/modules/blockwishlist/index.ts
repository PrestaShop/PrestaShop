import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : blockwishlist, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class Blockwishlist extends ModuleConfiguration {
  public readonly pageTitle: string;

  private readonly headTabs: string;

  private readonly headTab: string;

  private readonly headTabNamed: (name: string) => string;

  /**
   * @constructs
   */
  constructor() {
    super();

    this.pageTitle = `Configuration • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.headTabs = '#head_tabs';
    this.headTab = `${this.headTabs} .nav-item`;
    this.headTabNamed = (name: string) => `${this.headTab} #subtab-Wishlist${name}AdminController`;
  }

  // Methods
  /**
   * @param page {Page}
   * @returns Promise<void>
   */
  async goToStatisticsTab(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.headTabNamed('Statistics'));
  }

  /**
   * @param page {Page}
   * @param name {'Configuration'|'Statistics'}
   * @returns Promise<boolean>
   */
  async isTabActive(page: Page, name: 'Configuration'|'Statistics'): Promise<boolean> {
    return this.elementVisible(page, `${this.headTabNamed(name)}.active.current`, 1000);
  }
}

export default new Blockwishlist();
