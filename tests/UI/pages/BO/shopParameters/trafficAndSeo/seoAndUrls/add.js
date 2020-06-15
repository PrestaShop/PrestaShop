require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddSeoUrl extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'SEO & URLs • ';

    // Selectors
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
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(lang = 'en') {
    await Promise.all([
      this.page.click(this.pageTitleLangButton),
      this.waitForVisibleSelector(`${this.pageTitleLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      this.page.click(this.pageTitleLangSpan(lang)),
      this.waitForVisibleSelector(`${this.pageTitleLangButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Delete all keywords
   * @param lang, to specify which input to empty
   * @return {Promise<void>}
   */
  async deleteKeywords(lang = 'en') {
    const closeButtons = await this.page.$$(this.deleteKeywordLink(lang));
    for (let i = 0; i < closeButtons.length; i++) {
      await closeButtons[i].click();
    }
  }

  /**
   * Add keywords
   * @param keywords, array of keywords
   * @param idLang, to choose which lang (1 for en, 2 for fr)
   * @return {Promise<void>}
   */
  async addKeywords(keywords, idLang = 1) {
    for (let i = 0; i < keywords.length; i++) {
      await this.page.type(this.metaKeywordsInput(idLang), keywords[i]);
      await this.page.keyboard.press('Enter');
    }
  }

  /**
   *
   * @param seoPageData
   * @return {Promise<void>}
   */
  async createEditSeoPage(seoPageData) {
    await this.page.selectOption(this.pageNameSelect, seoPageData.page);
    // Fill form in english
    await this.changeLanguageForSelectors('en');
    await this.setValue(this.pageTitleInput(1), seoPageData.title);
    await this.setValue(this.metaDescriptionInput(1), seoPageData.metaDescription);
    await this.deleteKeywords('en');
    await this.addKeywords(seoPageData.metaKeywords, 1);
    await this.setValue(this.friendlyUrlInput(1), seoPageData.friendlyUrl);
    // Fill form in french
    await this.changeLanguageForSelectors('fr');
    await this.setValue(this.pageTitleInput(2), seoPageData.frTitle);
    await this.setValue(this.metaDescriptionInput(2), seoPageData.frMetaDescription);
    await this.deleteKeywords('fr');
    await this.addKeywords(seoPageData.frMetaKeywords, 2);
    await this.setValue(this.friendlyUrlInput(2), seoPageData.frFriendlyUrl);

    // Save seo page
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
