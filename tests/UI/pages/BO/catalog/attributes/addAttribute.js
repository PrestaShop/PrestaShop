require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddAttribute extends BOBasePage {
  constructor() {
    super();

    this.createPageTitle = 'Attributes > Add New Attribute • ';
    this.editPageTitle = 'Attributes > Edit:';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Form selectors
    this.nameInput = '#name_1';
    this.publicNameInput = '#public_name_1';
    this.urlInput = 'input[name=\'url_name_1\']';
    this.metaTitleInput = 'input[name=\'meta_title_1\']';
    this.indexableToggle = toggle => `label[for='indexable_${toggle}']`;
    this.attributeTypeSelect = '#group_type';
    this.saveButton = '#attribute_group_form_submit_btn';
  }

  /**
   * Fill attribute form and save it
   * @param page
   * @param attributeData
   * @return {Promise<string>}
   */
  async addEditAttribute(page, attributeData) {
    // Set names
    await this.setValue(page, this.nameInput, attributeData.name);
    await this.setValue(page, this.publicNameInput, attributeData.publicName);

    // Set Url and meta title
    await this.setValue(page, this.urlInput, attributeData.url);
    await this.setValue(page, this.metaTitleInput, attributeData.metaTitle);

    // Set indexable toggle
    await page.click(this.indexableToggle(attributeData.indexable ? 'on' : 'off'));

    // Set attribute type
    await this.selectByVisibleText(page, this.attributeTypeSelect, attributeData.attributeType);

    // Save attribute
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddAttribute();
