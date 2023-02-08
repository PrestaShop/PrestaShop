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

  private readonly pageTitleLangButton: string;

  private readonly pageTitleLangSpan: (lang: string) => string;

  private readonly pageNameSelect: string;

  private readonly pageTitleInput: (id: number) => string;

  private readonly metaDescriptionInput: (id: number) => string;

  private readonly metaKeywordsInput: (id: number) => string;

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

    this.pageTitle = 'SEO & URLs • ';

    // Selectors
    this.pageTitleLangButton = '#meta_page_title_dropdown';
    this.pageTitleLangSpan = (lang: string) => 'div.dropdown-menu[aria-labelledby=\'meta_page_title_dropdown\']'
      + ` span[data-locale='${lang}']`;
    this.pageNameSelect = '#meta_page_name';
    this.pageTitleInput = (id: number) => `#meta_page_title_${id}`;
    this.metaDescriptionInput = (id: number) => `#meta_meta_description_${id}`;
    this.metaKeywordsInput = (id: number) => `#meta_meta_keywords_${id}-tokenfield`;
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
   * Delete all keywords
   * @param page {Page} Browser tab
   * @param lang {string} To specify which input to empty
   * @return {Promise<void>}
   */
  async deleteKeywords(page: Page, lang: string = 'en'): Promise<void> {
    const closeButtons = await page.$$(this.deleteKeywordLink(lang));

    for (let i = 0; i < closeButtons.length; i++) {
      await closeButtons[i].click();
    }
  }

  /**
   * Add keywords
   * @param page {Page} Browser tab
   * @param keywords {array} Array of keywords
   * @param idLang {number} To choose which lang (1 for en, 2 for fr)
   * @return {Promise<void>}
   */
  async addKeywords(page: Page, keywords: string[], idLang: number = 1): Promise<void> {
    for (let i = 0; i < keywords.length; i++) {
      await page.type(this.metaKeywordsInput(idLang), keywords[i]);
      await page.keyboard.press('Enter');
    }
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
    await this.deleteKeywords(page, 'en');
    await this.addKeywords(page, seoPageData.metaKeywords, 1);
    await this.setValue(page, this.friendlyUrlInput(1), seoPageData.friendlyUrl);
    // Fill form in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.pageTitleInput(2), seoPageData.frTitle);
    await this.setValue(page, this.metaDescriptionInput(2), seoPageData.frMetaDescription);
    await this.deleteKeywords(page, 'fr');
    await this.addKeywords(page, seoPageData.frMetaKeywords, 2);
    await this.setValue(page, this.friendlyUrlInput(2), seoPageData.frFriendlyUrl);

    // Save seo page
    await this.clickAndWaitForURL(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddSeoUrl();
