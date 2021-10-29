require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add brand page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddBrand extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on add brand page
   */
  constructor() {
    super();

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
    this.statusToggleInput = toggle => `#manufacturer_is_enabled_${toggle}`;

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
   * @param page {Page} Browser tab
   * @param brandData {BrandData} Data to set in brand form
   * @returns {Promise<string>}
   */
  async createEditBrand(page, brandData) {
    // Fill Name
    await this.setValue(page, this.nameInput, brandData.name);
    // Fill information in english
    await this.changeLanguage(page, 'en');
    await this.setValueOnTinymceInput(page, this.shortDescriptionIFrame(1), brandData.shortDescription);
    await this.setValueOnTinymceInput(page, this.descriptionIFrame(1), brandData.description);
    await this.setValue(page, this.metaTitleInput(1), brandData.metaTitle);
    await this.setValue(page, this.metaDescriptionInput(1), brandData.metaDescription);
    await this.deleteKeywords(page, 'en');
    await this.addKeywords(page, brandData.metaKeywords, 1);

    // Fill Information in french
    await this.changeLanguage(page, 'fr');
    await this.setValueOnTinymceInput(page, this.shortDescriptionIFrame(2), brandData.shortDescriptionFr);
    await this.setValueOnTinymceInput(page, this.descriptionIFrame(2), brandData.descriptionFr);
    await this.setValue(page, this.metaTitleInput(2), brandData.metaTitleFr);
    await this.setValue(page, this.metaDescriptionInput(2), brandData.metaDescriptionFr);
    await this.deleteKeywords(page, 'fr');
    await this.addKeywords(page, brandData.metaKeywordsFr, 2);

    // Add logo
    await this.uploadFile(page, this.logoFileInput, brandData.logo);

    // Set Enabled value
    await page.check(this.statusToggleInput(brandData.enabled ? 1 : 0));

    // Save Created brand
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete all keywords
   * @param page {Page} Browser tab
   * @param lang {string} To specify which input to empty
   * @return {Promise<void>}
   */
  async deleteKeywords(page, lang = 'en') {
    const closeButtons = await page.$$(this.deleteKeywordLink(lang));

    /* eslint-disable no-await-in-loop, no-restricted-syntax */
    for (const closeButton of closeButtons) {
      await closeButton.click();
    }
    /* eslint-enable no-await-in-loop, no-restricted-syntax */
  }

  /**
   * Add keywords
   * @param page {Page} Browser tab
   * @param keywords {Array<string>} Array of keywords
   * @param id {number} ID for lang (1 for en, 2 for fr)
   * @return {Promise<void>}
   */
  async addKeywords(page, keywords, id = 1) {
    /* eslint-disable no-await-in-loop, no-restricted-syntax */
    for (const keyword of keywords) {
      await page.type(this.metaKeywordsInput(id), keyword);
      await page.keyboard.press('Enter');
    }
    /* eslint-enable no-await-in-loop, no-restricted-syntax */
  }

  /**
   * Change language for selector
   * @param page {Page} Browser tab
   * @param lang {string} Language to choose
   * @return {Promise<void>}
   */
  async changeLanguage(page, lang) {
    await Promise.all([
      page.$eval(this.shortDescriptionLangLink(lang), el => el.click()),
      this.waitForVisibleSelector(page, `${this.shortDescriptionLangLink(lang)}.active`),
    ]);
  }
}

module.exports = new AddBrand();
