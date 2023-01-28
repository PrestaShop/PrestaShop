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
   * Go to languages page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabLanguages(page: Page): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.languagesNavItemLink);
  }

  /**
   * Go to currencies page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabCurrencies(page: Page): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.currenciesNavItemLink);
  }
};
