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

  private readonly pagesConfigurationNavItemLink: string;

  private readonly themeShopCard: string;

  private readonly cardInactiveTheme: string;

  private readonly useThemeButton: string;

  private readonly useThemeModalDialog: string;

  protected readonly useThemeModalDialogYesButton: string;

  private readonly deleteThemeButton: string;

  private readonly deleteThemeModalDialog: string;

  private readonly deleteThemeModalDialogYesButton: string;

  protected growlDiv: string;

  protected growlMessageBlock: string;

  protected growlCloseButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on theme & logo page
   */
  constructor() {
    super();
    this.pagesConfigurationNavItemLink = '#subtab-AdminPsThemeCustoConfiguration';
    this.advancedCustomizationNavItemLink = '#subtab-AdminPsThemeCustoAdvanced';
    this.themeShopCard = '.card-header[data-role="theme-shop"]';
    this.cardInactiveTheme = '.card-body :nth-child(2) .theme-card[data-role="theme-card-container"]';
    this.useThemeButton = '.action-button.js-display-use-theme-modal';
    this.useThemeModalDialog = '#use_theme_modal .modal-dialog';
    this.useThemeModalDialogYesButton = `${this.useThemeModalDialog} .js-submit-use-theme`;
    this.deleteThemeButton = '.delete-button';
    this.deleteThemeModalDialog = '#delete_theme_modal .modal-dialog';
    this.deleteThemeModalDialogYesButton = `${this.deleteThemeModalDialog} .js-submit-delete-theme`;

    // Growls
    this.growlDiv = '#growls';
    this.growlMessageBlock = `${this.growlDiv} .growl-message`;
    this.growlCloseButton = `${this.growlDiv} .growl-close`;
  }

  /* Methods */
  /**
   * Get growl message content
   * @param page {Page} Browser tab
   * @param timeout {number} Timeout to wait for the selector
   * @return {Promise<string|null>}
   */
  async getGrowlMessageContent(page: Page, timeout: number = 10000): Promise<string | null> {
    return page.textContent(this.growlMessageBlock, {timeout});
  }

  /**
   * Close growl message
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async closeGrowlMessage(page: Page): Promise<void> {
    let growlNotVisible = await this.elementNotVisible(page, this.growlMessageBlock, 10000);

    while (!growlNotVisible) {
      try {
        await page.locator(this.growlCloseButton).click();
      } catch (e) {
        // If element does not exist it's already not visible
      }

      growlNotVisible = await this.elementNotVisible(page, this.growlMessageBlock, 2000);
    }

    await this.waitForHiddenSelector(page, this.growlMessageBlock);
  }

  /**
   * Go to pages configuration page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabPagesConfiguration(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.pagesConfigurationNavItemLink);
  }

  /**
   * Go to advanced customization page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabAdvancedCustomization(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.advancedCustomizationNavItemLink);
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
}
