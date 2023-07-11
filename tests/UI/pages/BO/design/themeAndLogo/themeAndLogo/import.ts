import themeAndLogoBasePage from '@pages/BO/design/themeAndLogo/themeAndLogo/themeAndLogoBasePage';

import type {Page} from 'playwright';

/**
 * Theme import page, contains functions that can be used on the page
 * @class
 * @extends themeAndLogoBasePage
 */
class ImportTheme extends themeAndLogoBasePage {
  public readonly pageTitle: string;

  private readonly importForm: string;

  private readonly inputArchiveURL: string;

  private readonly importWebSubmit: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on theme & logo page
   */
  constructor() {
    super();

    this.pageTitle = `Theme import • ${global.INSTALL.SHOP_NAME}`;

    this.importForm = 'form[name="import_theme"]';
    // Form "Import from the web"
    this.inputArchiveURL = '#import_theme_import_from_web';
    this.importWebSubmit = `${this.importForm} > div.row > div:nth-child(2) div.card-footer button`;
  }

  /**
   * Import theme from web
   * @param page  {Page} Browser tab
   * @param themeUrl {string} Theme URL link to import
   * @returns {Promise<void>}
   */
  async importTheme(page: Page, themeUrl: string): Promise<void> {
    await this.setValue(page, this.inputArchiveURL, themeUrl);
    await page.click(this.importWebSubmit);
  }
}

export default new ImportTheme();
