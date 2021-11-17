require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add feature page, contains functions that can be used on add feature page
 * @class
 * @extends BOBasePage
 */
class AddFeature extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add feature page
   */
  constructor() {
    super();

    this.createPageTitle = 'Features > Add New Feature •';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Form selectors
    this.nameInput = '#name_1';
    this.urlInput = 'input[name=\'url_name_1\']';
    this.metaTitleInput = 'input[name=\'meta_title_1\']';
    this.indexableToggle = toggle => `#indexable_${toggle}`;
    this.saveButton = '#feature_form_submit_btn';
  }

  /**
   * Fill feature form and save it
   * @param page {Page} Browser tab
   * @param featureData {FeatureData} Values to set on add feature form inputs
   * @return {Promise<string>}
   */
  async setFeature(page, featureData) {
    // Set name
    await this.setValue(page, this.nameInput, featureData.name);

    // Set Url and meta title
    await this.setValue(page, this.urlInput, featureData.url);
    await this.setValue(page, this.metaTitleInput, featureData.metaTitle);

    // Set indexable toggle
    await page.check(this.indexableToggle(featureData.indexable ? 'on' : 'off'));

    // Save feature
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddFeature();
