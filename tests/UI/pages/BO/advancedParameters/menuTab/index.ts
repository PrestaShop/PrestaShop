import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * MenuTab page, should not be displayed on BO
 * @class
 * @extends BOBasePage
 */
class MenuTab extends BOBasePage {
  public readonly pageTitle: string;

  private readonly pageH1Title: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on MenuTab page
   */
  constructor() {
    super();

    this.pageTitle = 'Menus';

    // Selectors
    this.alertDangerBlockParagraph = '.alert-danger';
    this.pageH1Title = 'h1.page-title';
  }

  // Functions

  /**
   * @override
   * Get title from selector instead of header
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPageTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.pageH1Title);
  }
}

export default new MenuTab();
