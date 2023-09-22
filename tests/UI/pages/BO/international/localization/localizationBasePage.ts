import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Localization base page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
export default class LocalizationBasePage extends BOBasePage {
  private readonly localizationNavItemLink: string;

  private readonly languagesNavItemLink: string;

  private readonly currenciesNavItemLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on localization page
   */
  constructor() {
    super();

    this.localizationNavItemLink = '#subtab-AdminLocalization';
    this.languagesNavItemLink = '#subtab-AdminLanguages';
    this.currenciesNavItemLink = '#subtab-AdminCurrencies';
  }

  /* Header Methods */
  /**
   * Go to Localization tab
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabLocalizations(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.localizationNavItemLink);
  }

  /**
   * Go to Languages tab
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabLanguages(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.languagesNavItemLink);
  }

  /**
   * Go to Currencies tab
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabCurrencies(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.currenciesNavItemLink);
  }
}
