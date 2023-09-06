import BOBasePage from '@pages/BO/BObasePage';

import type FeatureData from '@data/faker/feature';

import type {Page} from 'playwright';

/**
 * Add feature page, contains functions that can be used on add feature page
 * @class
 * @extends BOBasePage
 */
class AddFeature extends BOBasePage {
  public readonly createPageTitle: string;

  private readonly featureForm: string;

  private readonly nameInput: string;

  private readonly urlInput: string;

  private readonly metaTitleInput: string;

  private readonly indexableToggle: (toggle: string) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add feature page
   */
  constructor() {
    super();

    this.createPageTitle = `New feature • ${global.INSTALL.SHOP_NAME}`;

    // Form selectors
    this.featureForm = 'form[name="feature"]';
    this.nameInput = `${this.featureForm} #feature_name_1`;
    this.urlInput = `${this.featureForm} #feature_url_name_1`;
    this.metaTitleInput = `${this.featureForm} #feature_meta_title_1`;
    this.indexableToggle = (toggle: string) => `${this.featureForm} #feature_layered_indexable_${toggle}`;
    this.saveButton = `${this.featureForm} #save-button`;
  }

  /**
   * Fill feature form and save it
   * @param page {Page} Browser tab
   * @param featureData {FeatureData} Values to set on add feature form inputs
   * @return {Promise<string>}
   */
  async setFeature(page: Page, featureData: FeatureData): Promise<string> {
    // Set name
    await this.setValue(page, this.nameInput, featureData.name);

    // Set Url and meta title
    await this.setValue(page, this.urlInput, featureData.url);
    await this.setValue(page, this.metaTitleInput, featureData.metaTitle);

    // Set indexable toggle
    await this.setChecked(page, this.indexableToggle(featureData.indexable ? '1' : '0'));

    // Save feature
    await this.clickAndWaitForURL(page, this.saveButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddFeature();
