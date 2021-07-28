require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add tag page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddTag extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on add tag page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Tags > Add new •';
    this.pageTitleEdit = 'Tags > Edit:';

    // selectors
    this.nameInput = '#name';
    this.languageInput = '#id_lang';
    this.productSelect = '#select_left';
    this.moveToLeftButton = '#move_to_left';
    this.moveToRightButton = '#move_to_right';
    this.saveButton = '#tag_form_submit_btn';
  }

  /* Methods */
  /**
   * Create/Edit tag
   * @param page {Page} Browser tab
   * @param tagData {TagData} Data to set to tag form
   * @returns {Promise<void>}
   */
  async setTag(page, tagData) {
    await this.setValue(page, this.nameInput, tagData.name);
    await this.selectByVisibleText(page, this.languageInput, tagData.language);
    // Choose product
    await this.waitForSelectorAndClick(page, this.moveToLeftButton);
    await this.selectByVisibleText(page, this.productSelect, tagData.products);
    await this.waitForSelectorAndClick(page, this.moveToRightButton);

    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddTag();
