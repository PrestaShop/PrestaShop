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

  private readonly importFromYourComputerSubmit: string;

  private readonly zipFileUploadButton: string;

  private readonly selectArchive: string;

  private readonly importFTPSubmit: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on theme & logo page
   */
  constructor() {
    super();

    this.pageTitle = `Theme import • ${global.INSTALL.SHOP_NAME}`;

    this.importForm = 'form[name="import_theme"]';
    // Form "Import from your computer"
    this.zipFileUploadButton = '#import_theme_import_from_computer';
    this.importFromYourComputerSubmit = `${this.importForm} div.row div:nth-child(1) div.card-footer button`;

    // Form "Import from the web"
    this.inputArchiveURL = '#import_theme_import_from_web';
    this.importWebSubmit = `${this.importForm} div.row div:nth-child(2) div.card-footer button`;

    // From FTP
    this.selectArchive = '#import_theme_import_from_ftp';
    this.importFTPSubmit = `${this.importForm} div.row div:nth-child(3) div.card-footer button`;
  }

  /**
   * Import theme from your computer
   * @param page {Page} Browser tab
   * @param path {string} Theme path to import
   * @returns {Promise<void>}
   */
  async importFromYourComputer(page: Page, path: string): Promise<void> {
    await this.uploadFile(page, this.zipFileUploadButton, path);
    await page.click(this.importFromYourComputerSubmit);
  }

  /**
   * Import theme from web
   * @param page {Page} Browser tab
   * @param themeUrl {string} Theme URL link to import
   * @returns {Promise<void>}
   */
  async importFromWeb(page: Page, themeUrl: string): Promise<void> {
    await this.setValue(page, this.inputArchiveURL, themeUrl);
    await page.click(this.importWebSubmit);
  }

  /**
   * Import theme from ftp
   * @param page {Page} Browser tab
   * @param zipName {string} Zip name to select
   * @returns {Promise<void>}
   */
  async importFromFTP(page: Page, zipName: string): Promise<void> {
    await this.selectByVisibleText(page, this.selectArchive, zipName);
    await page.click(this.importFTPSubmit);
  }
}

export default new ImportTheme();
