import themeAndLogoBasePage from '@pages/BO/design/themeAndLogo/themeAndLogo/themeAndLogoBasePage';

import type {Page} from 'playwright';

/**
 * Theme & logo page, contains functions that can be used on the page
 * @class
 * @extends themeAndLogoBasePage
 */
class ThemeAndLogo extends themeAndLogoBasePage {
  public readonly pageTitle: string;

  private readonly addNewThemeButton: string;

  private readonly themeCardContainer: string;

  private readonly useSpecificThemeButton: (name: string) => string;

  private readonly removeSpecificThemeButton: (name: string) => string;

  private readonly removeThemeModalDialog: string;

  private readonly removeThemeModalDialogYesButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on theme & logo page
   */
  constructor() {
    super();

    this.pageTitle = `Theme & Logo • ${global.INSTALL.SHOP_NAME}`;
    this.addNewThemeButton = '#page-header-desc-configuration-add';
    this.themeCardContainer = '#themes-logo-page .theme-card-container';
    this.useSpecificThemeButton = (name: string) => `${this.themeCardContainer}[data-role="${name}"] `
      + 'button.js-display-use-theme-modal';
    this.removeSpecificThemeButton = (name: string) => `${this.themeCardContainer}[data-role="${name}"] `
      + 'button.js-display-delete-theme-modal';
    this.removeThemeModalDialog = '#delete_theme_modal .modal-dialog';
    this.removeThemeModalDialogYesButton = `${this.removeThemeModalDialog} .js-submit-delete-theme`;
  }

  /**
   * Go to "Add new theme" page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToNewThemePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewThemeButton);
  }

  /**
   * Returns the number of themes
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfThemes(page: Page): Promise<number> {
    return (await page.$$(this.themeCardContainer)).length;
  }

  /**
   * Enable a specific theme
   * @param page {Page} Browser tab
   * @param themeName {string} Theme name
   * @returns {Promise<String>}
   */
  async enableTheme(page: Page, themeName: string): Promise<string> {
    await page.locator(this.useSpecificThemeButton(themeName)).evaluate((el: HTMLElement) => el.click());
    await this.waitForSelectorAndClick(page, this.useThemeModalDialogYesButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Remove a specific theme
   * @param page {Page} Browser tab
   * @param themeName {string} Theme name
   * @returns {Promise<String>}
   */
  async removeTheme(page: Page, themeName: string): Promise<string> {
    await page.locator(this.removeSpecificThemeButton(themeName)).evaluate((el: HTMLElement) => el.click());
    await this.waitForSelectorAndClick(page, this.removeThemeModalDialogYesButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new ThemeAndLogo();
