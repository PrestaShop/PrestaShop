require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddBrand extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Add new • ';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.nameInput = '#manufacturer_name';
    this.shortDescriptionDiv = '#manufacturer_short_description';
    this.shortDescriptionLangLink = lang => `${this.shortDescriptionDiv} li.nav-item a[data-locale='${lang}']`;
    this.shortDescriptionIFrame = id => `${this.shortDescriptionDiv} #manufacturer_short_description_${id}_ifr`;
    this.descriptionDiv = '#manufacturer_description';
    this.descriptionIFrame = id => `${this.descriptionDiv} #manufacturer_description_${id}_ifr`;
    this.logoFileInput = '#manufacturer_logo';
    this.metaTitleInput = id => `#manufacturer_meta_title_${id}`;
    this.metaDescriptionInput = id => `#manufacturer_meta_description_${id}`;
    this.metaKeywordsInput = id => `#manufacturer_meta_keyword_${id}-tokenfield`;
    this.enabledSwitchLabel = id => `label[for='manufacturer_is_enabled_${id}']`;
    // Selectors for Meta keywords
    this.taggableFieldDiv = lang => `div.input-group div.js-locale-${lang}`;
    this.deleteKeywordLink = lang => `${this.taggableFieldDiv(lang)} a.close`;
    this.saveButton = '.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Create or edit Brand
   * @param brandData
   * @return {Promise<void>}
   */
  async createEditBrand(brandData) {
    // Fill Name
    await this.setValue(this.nameInput, brandData.name);
    // Fill information in english
    await this.changeLanguage('en');
    await this.setValueOnTinymceInput(this.shortDescriptionIFrame(1), brandData.shortDescription);
    await this.setValueOnTinymceInput(this.descriptionIFrame(1), brandData.description);
    await this.setValue(this.metaTitleInput(1), brandData.metaTitle);
    await this.setValue(this.metaDescriptionInput(1), brandData.metaDescription);
    await this.deleteKeywords('en');
    await this.addKeywords(brandData.metaKeywords, 1);

    // Fill Information in french
    await this.changeLanguage('fr');
    await this.setValueOnTinymceInput(this.shortDescriptionIFrame(2), brandData.shortDescriptionFr);
    await this.setValueOnTinymceInput(this.descriptionIFrame(2), brandData.descriptionFr);
    await this.setValue(this.metaTitleInput(2), brandData.metaTitleFr);
    await this.setValue(this.metaDescriptionInput(2), brandData.metaDescriptionFr);
    await this.deleteKeywords('fr');
    await this.addKeywords(brandData.metaKeywordsFr, 2);

    // Add logo
    await this.generateAndUploadImage(this.logoFileInput, brandData.logo);

    // Set Enabled value
    await this.page.click(this.enabledSwitchLabel(brandData.enabled ? 1 : 0));
    // Save Created brand
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all keywords
   * @param lang, to specify which input to empty
   * @return {Promise<void>}
   */
  async deleteKeywords(lang = 'en') {
    const closeButtons = await this.page.$$(this.deleteKeywordLink(lang));
    /* eslint-disable no-await-in-loop, no-restricted-syntax */
    for (const closeButton of closeButtons) {
      await closeButton.click();
    }
    /* eslint-enable no-await-in-loop, no-restricted-syntax */
  }

  /**
   * Add keywords
   * @param keywords, array of keywords
   * @param id, to choose which lang (1 for en, 2 for fr)
   * @return {Promise<void>}
   */
  async addKeywords(keywords, id = '1') {
    /* eslint-disable no-await-in-loop, no-restricted-syntax */
    for (const keyword of keywords) {
      await this.page.type(this.metaKeywordsInput(id), keyword);
      await this.page.keyboard.press('Enter');
    }
    /* eslint-enable no-await-in-loop, no-restricted-syntax */
  }

  /**
   * Change language for selector
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguage(lang) {
    await Promise.all([
      this.page.$eval(this.shortDescriptionLangLink(lang), el => el.click()),
      this.waitForVisibleSelector(`${this.shortDescriptionLangLink(lang)}.active`),
    ]);
  }
};
