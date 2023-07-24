import BOBasePage from '@pages/BO/BObasePage';

import type FeatureData from '@data/faker/feature';

import type {Page} from 'playwright';

/**
 * Edit feature page, contains functions that can be used on edit feature page
 * @class
 * @extends BOBasePage
 */
class EditFeature extends BOBasePage {
  public readonly editPageTitle: string;

  private readonly nameInput: string;

  private readonly urlInput: string;

  private readonly metaTitleInput: string;

  private readonly indexableToggle: (toggle: string) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on edit feature page
   */
  constructor() {
    super();

    this.editPageTitle = 'Edit:';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Form selectors
    this.nameInput = '#name_1';
    this.urlInput = 'input[name=\'url_name_1\']';
    this.metaTitleInput = 'input[name=\'meta_title_1\']';
    this.indexableToggle = (toggle: string) => `#indexable_${toggle}`;
    this.saveButton = '#feature_form_submit_btn';
  }

  /**
   * Edit feature and save it
   * @param page {Page} Browser tab
   * @param featureData {FeatureData} Values to set on edit feature form inputs
   * @return {Promise<string>}
   */
  async editFeature(page: Page, featureData: FeatureData): Promise<string> {
    // Set name
    await this.setValue(page, this.nameInput, featureData.name);

    // Set Url and meta title
    await this.setValue(page, this.urlInput, featureData.url);
    await this.setValue(page, this.metaTitleInput, featureData.metaTitle);

    // Set indexable toggle
    await this.setChecked(page, this.indexableToggle(featureData.indexable ? 'on' : 'off'));

    // Save feature
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new EditFeature();
