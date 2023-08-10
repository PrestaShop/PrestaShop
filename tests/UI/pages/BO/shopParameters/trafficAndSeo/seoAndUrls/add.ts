import BOBasePage from '@pages/BO/BObasePage';

import SeoPageData from '@data/faker/seoPage';

import type {Page} from 'playwright';

/**
 * Add seo and url page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddSeoUrl extends BOBasePage {
  public readonly pageTitle: string;

  public readonly editPageTitle: string;

  private readonly pageTitleLangButton: string;

  private readonly pageTitleLangSpan: (lang: string) => string;

  private readonly pageNameSelect: string;

  private readonly pageTitleInput: (id: number) => string;

  private readonly metaDescriptionInput: (id: number) => string;

  private readonly friendlyUrlInput: (id: number) => string;

  private readonly taggableFieldDiv: (lang: string) => string;

  private readonly deleteKeywordLink: (lang: string) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on add seo and url page
   */
  constructor() {
    super();

    this.pageTitle = `New page configuration • ${global.INSTALL.SHOP_NAME}`;
    this.editPageTitle = 'Editing configuration for';

    // Selectors
    this.pageTitleLangButton = '#meta_page_title_dropdown';
    this.pageTitleLangSpan = (lang: string) => 'div.dropdown-menu[aria-labelledby=\'meta_page_title_dropdown\']'
      + ` span[data-locale='${lang}']`;
    this.pageNameSelect = '#meta_page_name';
    this.pageTitleInput = (id: number) => `#meta_page_title_${id}`;
    this.metaDescriptionInput = (id: number) => `#meta_meta_description_${id}`;
    this.friendlyUrlInput = (id: number) => `#meta_url_rewrite_${id}`;

    // Selectors for Meta keywords
    this.taggableFieldDiv = (lang: string) => `div.input-group div.js-locale-${lang}`;
    this.deleteKeywordLink = (lang: string) => `${this.taggableFieldDiv(lang)} a.close`;
    this.saveButton = '#save-button';
  }

  /* Methods */

  /**
   * Change language for selectors
   * @param page {Page} Browser tab
   * @param lang {string} Language to change
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(page: Page, lang: string = 'en'): Promise<void> {
    await Promise.all([
      page.click(this.pageTitleLangButton),
      this.waitForVisibleSelector(page, `${this.pageTitleLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.pageTitleLangSpan(lang)),
      this.waitForVisibleSelector(page, `${this.pageTitleLangButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Create/Edit seo page
   * @param page {Page} Browser tab
   * @param seoPageData {SeoPageData} Data to set on seo form
   * @return {Promise<void>}
   */
  async createEditSeoPage(page: Page, seoPageData: SeoPageData): Promise<string> {
    await page.selectOption(this.pageNameSelect, seoPageData.page);
    // Fill form in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.pageTitleInput(1), seoPageData.title);
    await this.setValue(page, this.metaDescriptionInput(1), seoPageData.metaDescription);
    await this.setValue(page, this.friendlyUrlInput(1), seoPageData.friendlyUrl);
    // Fill form in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.pageTitleInput(2), seoPageData.frTitle);
    await this.setValue(page, this.metaDescriptionInput(2), seoPageData.frMetaDescription);
    await this.setValue(page, this.friendlyUrlInput(2), seoPageData.frFriendlyUrl);

    // Save seo page
    await this.clickAndWaitForURL(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddSeoUrl();
