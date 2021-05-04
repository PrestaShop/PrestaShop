require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddValue extends BOBasePage {
  constructor() {
    super();

    this.createPageTitle = 'Attributes > Add New Value • ';
    this.editPageTitle = 'Attributes > Edit:';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Form selectors
    this.attributeGroupSelect = '#id_attribute_group';
    this.valueInput = '#name_1';
    this.urlInput = 'input[name=\'url_name_1\']';
    this.metaTitleInput = 'input[name=\'meta_title_1\']';
    this.colorInput = '#color_0';
    this.textureFileInput = '#texture-name';
    this.saveButton = '#attribute_form_submit_btn';
    this.saveAndStayButton = 'button[name=\'submitAddattributeAndStay\']';
  }

  /**
   * Fill value form and save it
   * @param page
   * @param valueData
   * @param saveAndStay
   * @return {Promise<string>}
   */
  async addEditValue(page, valueData, saveAndStay = false) {
    // Set group and value
    await this.selectByVisibleText(page, this.attributeGroupSelect, valueData.attributeName);
    await this.setValue(page, this.valueInput, valueData.value);

    // Set Url and meta title
    await this.setValue(page, this.urlInput, valueData.url);
    await this.setValue(page, this.metaTitleInput, valueData.metaTitle);

    // Set color and texture inputs
    if (await this.elementVisible(page, this.colorInput, 1000)) {
      await this.setValue(page, this.colorInput, valueData.color);
      //await this.uploadFile(this.textureFileInput, valueData.textureFileName);
    }

    // Save value
    if (saveAndStay) {
      await this.clickAndWaitForNavigation(page, this.saveAndStayButton);
    } else {
      await this.clickAndWaitForNavigation(page, this.saveButton);
    }

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddValue();
