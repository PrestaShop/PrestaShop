require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddSeoUrl extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'SEO & URLs • ';

    // Selectors
    this.pageTitleLangButton = '#meta_page_title';
    this.pageTitleLangSpan = 'div.dropdown-menu[aria-labelledby=\'meta_page_title\'] span[data-locale=\'%LANG\']';
    this.pageNameSelect = '#meta_page_name';
    this.pageTitleInput = '#meta_page_title_%ID';
    this.metaDescriptionInput = '#meta_meta_description_%ID';
    this.metaKeywordsInput = '#meta_meta_keywords_%ID-tokenfield';
    this.friendlyUrlInput = '#meta_url_rewrite_%ID';
    // Selectors for Meta keywords
    this.taggableFieldDiv = 'div.input-group div.js-locale-%LANG';
    this.deleteKeywordLink = `${this.taggableFieldDiv} a.close`;
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
      this.page.waitForSelector(`${this.pageTitleLangButton}[aria-expanded='true']`, {visible: true}),
    ]);
    await Promise.all([
      this.page.click(this.pageTitleLangSpan.replace('%LANG', lang)),
      this.page.waitForSelector(`${this.pageTitleLangButton}[aria-expanded='false']`, {visible: true}),
    ]);
  }

  /**
   * Delete all keywords
   * @param lang, to specify which input to empty
   * @return {Promise<void>}
   */
  async deleteKeywords(lang = 'en') {
    const closeButtons = await this.page.$$(this.deleteKeywordLink.replace('%LANG', lang));
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
      await this.page.type(this.metaKeywordsInput.replace('%ID', idLang), keywords[i]);
      await this.page.keyboard.press('Enter');
    }
  }

  /**
   *
   * @param seoPageData
   * @return {Promise<void>}
   */
  async createEditSeoPage(seoPageData) {
    await this.page.select(this.pageNameSelect, seoPageData.page);
    // Fill form in english
    await this.changeLanguageForSelectors('en');
    await this.setValue(this.pageTitleInput.replace('%ID', 1), seoPageData.title);
    await this.setValue(this.metaDescriptionInput.replace('%ID', 1), seoPageData.metaDescription);
    await this.deleteKeywords('en');
    await this.addKeywords(seoPageData.metaKeywords, 1);
    await this.setValue(this.friendlyUrlInput.replace('%ID', 1), seoPageData.friendlyUrl);
    // Fill form in french
    await this.changeLanguageForSelectors('fr');
    await this.setValue(this.pageTitleInput.replace('%ID', 2), seoPageData.frTitle);
    await this.setValue(this.metaDescriptionInput.replace('%ID', 2), seoPageData.frMetaDescription);
    await this.deleteKeywords('fr');
    await this.addKeywords(seoPageData.frMetaKeywords, 2);
    await this.setValue(this.friendlyUrlInput.replace('%ID', 2), seoPageData.frFriendlyUrl);

    // Save seo page
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
