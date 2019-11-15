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
    this.shortDescriptionLangLink = `${this.shortDescriptionDiv} li.nav-item a[data-locale='%LANG']`;
    this.shortDescriptionIFrame = `${this.shortDescriptionDiv} #manufacturer_short_description_%ID_ifr`;
    this.descriptionDiv = '#manufacturer_description';
    this.descriptionIFrame = `${this.descriptionDiv} #manufacturer_description_%ID_ifr`;
    this.logoFileInput = '#manufacturer_logo';
    this.metaTitleInput = '#manufacturer_meta_title_%ID';
    this.metaDescriptionInput = '#manufacturer_meta_description_%ID';
    this.metaKeyworkdsInput = '#manufacturer_meta_keyword_%ID-tokenfield';
    this.enabledSwitchlabel = 'label[for=\'manufacturer_is_enabled_%ID\']';
    // Selectors for Meta keywords
    this.taggableFieldDiv = 'div.input-group div.js-locale-%LANG';
    this.deleteKeywordLink = `${this.taggableFieldDiv} a.close`;
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
    await Promise.all([
      this.page.click(this.shortDescriptionLangLink.replace('%LANG', 'en')),
      this.page.waitForSelector(`${this.shortDescriptionLangLink.replace('%LANG', 'en')}.active`, {visible: true}),
    ]);
    await this.setValueOnTinymceInput(this.shortDescriptionIFrame.replace('%ID', '1'), brandData.shortDescription);
    await this.setValueOnTinymceInput(this.descriptionIFrame.replace('%ID', '1'), brandData.description);
    await this.setValueOnTinymceInput(this.metaTitleInput.replace('%ID', '1'), brandData.metaTitle);
    await this.setValueOnTinymceInput(this.metaDescriptionInput.replace('%ID', '1'), brandData.metaDescription);
    await this.deleteKeywords('en');
    await this.addKeywords(brandData.metaKeywords, '1');

    // Fill Information in french
    await Promise.all([
      this.page.click(this.shortDescriptionLangLink.replace('%LANG', 'fr')),
      this.page.waitForSelector(`${this.shortDescriptionLangLink.replace('%LANG', 'fr')}.active`, {visible: true}),
    ]);
    await this.setValueOnTinymceInput(this.shortDescriptionIFrame.replace('%ID', '2'), brandData.shortDescriptionFr);
    await this.setValueOnTinymceInput(this.descriptionIFrame.replace('%ID', '2'), brandData.descriptionFr);
    await this.setValueOnTinymceInput(this.metaTitleInput.replace('%ID', '2'), brandData.metaTitleFr);
    await this.setValueOnTinymceInput(this.metaDescriptionInput.replace('%ID', '2'), brandData.metaDescriptionFr);
    await this.deleteKeywords('fr');
    await this.addKeywords(brandData.metaKeywordsFr, '2');

    // Add logo
    await this.generateAndUploadImage(this.logoFileInput, brandData.logo);

    // Set Enabled value
    if (brandData.enabled) {
      await this.page.click(this.enabledSwitchlabel.replace('%ID', '1'));
    } else {
      await this.page.click(this.enabledSwitchlabel.replace('%ID', '0'));
    }
    // Save Created brand
    await this.clickAndWaitForNavigation(this.saveButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete all keywords
   * @param lang, to specify which input to empty
   * @return {Promise<void>}
   */
  async deleteKeywords(lang = 'en') {
    const closeButtons = await this.page.$$(this.deleteKeywordLink.replace('%LANG', lang));
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
      await this.page.type(this.metaKeyworkdsInput.replace('%ID', id), keyword);
      await this.page.keyboard.press('Enter');
    }
    /* eslint-enable no-await-in-loop, no-restricted-syntax */
  }
};
