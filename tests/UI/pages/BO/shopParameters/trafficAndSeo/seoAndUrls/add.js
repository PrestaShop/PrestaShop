require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddSeoUrl extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'SEO & URLs • ';

    // Selectors
    this.pageNameSpan = '#select2-meta_page_name-container';

    this.pageTitleLangButton = '#meta_page_title';
    this.pageTitleLangSpan = lang => 'div.dropdown-menu[aria-labelledby=\'meta_page_title\']'
      + ` span[data-locale='${lang}']`;
    this.pageNameSelect = '#meta_page_name';
    this.pageTitleInput = id => `#meta_page_title_${id}`;
    this.metaDescriptionInput = id => `#meta_meta_description_${id}`;
    this.metaKeywordsInput = id => `#meta_meta_keywords_${id}-tokenfield`;
    this.friendlyUrlInput = id => `#meta_url_rewrite_${id}`;
    // Selectors for Meta keywords
    this.taggableFieldDiv = lang => `div.input-group div.js-locale-${lang}`;
    this.deleteKeywordLink = lang => `${this.taggableFieldDiv(lang)} a.close`;
    this.saveButton = 'div.card-footer button';
  }

  /* Methods */

  /**
   * Change language for selectors
   * @param page
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(page, lang = 'en') {
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
   * @param page
   * @param lang, to specify which input to empty
   * @return {Promise<void>}
   */
  async deleteKeywords(page, lang = 'en') {
    const closeButtons = await page.$$(this.deleteKeywordLink(lang));
    for (let i = 0; i < closeButtons.length; i++) {
      await closeButtons[i].click();
    }
  }

  /**
   * Add keywords
   * @param page
   * @param keywords, array of keywords
   * @param idLang, to choose which lang (1 for en, 2 for fr)
   * @return {Promise<void>}
   */
  async addKeywords(page, keywords, idLang = 1) {
    for (let i = 0; i < keywords.length; i++) {
      await page.type(this.metaKeywordsInput(idLang), keywords[i]);
      await page.keyboard.press('Enter');
    }
  }

  /**
   * Create/Edit seo page
   * @param page
   * @param seoPageData
   * @return {Promise<void>}
   */
  async createEditSeoPage(page, seoPageData) {
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
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

module.exports = new AddSeoUrl();
