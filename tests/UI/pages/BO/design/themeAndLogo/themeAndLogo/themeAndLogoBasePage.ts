import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Theme & Logo base page, contains functions that can be used on the page
 * @class
 * @type {themeAndLogoBasePage}
 * @extends BOBasePage
 */
export default class themeAndLogoBasePage extends BOBasePage {
  private readonly advancedCustomizationNavItemLink: string;

  private readonly themeShopCard: string;

  private readonly cardInactiveTheme: string;

  private readonly useThemeButton: string;

  private readonly useThemeModalDialog: string;

  private readonly useThemeModalDialogYesButton: string;

  private readonly deleteThemeButton: string;

  private readonly deleteThemeModalDialog: string;

  private readonly deleteThemeModalDialogYesButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on theme & logo page
   */
  constructor() {
    super();
    this.advancedCustomizationNavItemLink = '#subtab-AdminPsThemeCustoAdvanced';
    this.themeShopCard = '.card-header[data-role="theme-shop"]';
    this.cardInactiveTheme = '.card-body :nth-child(2) .theme-card[data-role="theme-card-container"]';
    this.useThemeButton = '.action-button.js-display-use-theme-modal';
    this.useThemeModalDialog = '#use_theme_modal .modal-dialog';
    this.useThemeModalDialogYesButton = `${this.useThemeModalDialog} .js-submit-use-theme`;
    this.deleteThemeButton = '.delete-button';
    this.deleteThemeModalDialog = '#delete_theme_modal .modal-dialog';
    this.deleteThemeModalDialogYesButton = `${this.deleteThemeModalDialog} .js-submit-delete-theme`;
  }

  /* Methods */
  /**
   * Go to advanced customization page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabAdvancedCustomization(page: Page): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.advancedCustomizationNavItemLink);
  }

  /**
   * Use the Theme
   * @param page {Page} Browser tab
   * @returns {Promise<String>}
   */
  async useTheme(page: Page): Promise<string> {
    await this.scrollTo(page, this.themeShopCard);
    await page.hover(this.cardInactiveTheme);
    await this.waitForSelectorAndClick(page, this.useThemeButton);
    await this.waitForSelectorAndClick(page, this.useThemeModalDialogYesButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete the not used Theme
   * @param page {Page} Browser tab
   * @returns {Promise<String>}
   */
  async deleteTheme(page: Page): Promise<string> {
    await this.scrollTo(page, this.themeShopCard);
    await page.hover(this.cardInactiveTheme);
    await this.waitForSelectorAndClick(page, this.deleteThemeButton);
    await this.waitForSelectorAndClick(page, this.deleteThemeModalDialogYesButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
};
