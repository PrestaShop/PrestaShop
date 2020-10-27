require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddTag extends BOBasePage {
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
   * @param page
   * @param tagData
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
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new AddTag();
