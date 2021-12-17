require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add feature page, contains functions that can be used on add feature page
 * @class
 * @extends BOBasePage
 */
class AddValue extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add feature page
   */
  constructor() {
    super();

    this.createPageTitle = 'Features > Add New Feature •';
    this.editPageTitle = 'Features > Edit New Feature •';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Form selectors
    this.featureSelect = '#id_feature';
    this.valueInput = '#value_1';
    this.urlInput = 'input[name=\'url_name_1\']';
    this.metaTitleInput = 'input[name=\'meta_title_1\']';
    this.saveButton = '#feature_value_form_submit_btn';
    this.saveAndStayButton = 'button[name=\'submitAddfeature_valueAndStay\']';
  }

  /**
   * Fill value form and save it
   * @param page {Page} Browser tab
   * @param valueData {ValueData} Values to set on add feature value form inputs
   * @param saveAndStay {boolean} True if we need to save and stay
   * @return {Promise<string>}
   */
  // eslint-disable-next-line consistent-return
  async addEditValue(page, valueData, saveAndStay = false) {
    // Set group and value
    await this.selectByVisibleText(page, this.featureSelect, valueData.featureName);
    await this.setValue(page, this.valueInput, valueData.value);

    // Set Url and meta title
    await this.setValue(page, this.urlInput, valueData.url);
    await this.setValue(page, this.metaTitleInput, valueData.metaTitle);

    // Save value
    if (saveAndStay) {
      await this.clickAndWaitForNavigation(page, this.saveAndStayButton);
    } else {
      await this.clickAndWaitForNavigation(page, this.saveButton);
      // Return successful message
      return this.getAlertSuccessBlockParagraphContent(page);
    }
  }
}

module.exports = new AddValue();
