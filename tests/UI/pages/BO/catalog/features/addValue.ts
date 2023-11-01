import BOBasePage from '@pages/BO/BObasePage';

import type FeatureValueData from '@data/faker/featureValue';

import type {Page} from 'playwright';

/**
 * Add feature page, contains functions that can be used on add feature page
 * @class
 * @extends BOBasePage
 */
class AddValue extends BOBasePage {
  public readonly createPageTitle: string;

  public readonly editPageTitle: string;

  private readonly featureSelect: string;

  private readonly valueInput: string;

  private readonly urlInput: string;

  private readonly metaTitleInput: string;

  private readonly saveButton: string;

  private readonly saveAndStayButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add feature page
   */
  constructor() {
    super();

    this.createPageTitle = `New Feature Value • ${global.INSTALL.SHOP_NAME}`;
    this.editPageTitle = `Feature value • ${global.INSTALL.SHOP_NAME}`;

    // Form selectors
    this.featureSelect = '#feature_value_feature_id';
    this.valueInput = '#feature_value_value_1';
    this.urlInput = 'input[name=\'url_name_1\']';
    this.metaTitleInput = 'input[name=\'meta_title_1\']';
    this.saveButton = '#save-button';
    this.saveAndStayButton = 'button[name=\'save-and-add-new\']';
  }

  /**
   * Fill value form and save it
   * @param page {Page} Browser tab
   * @param valueData {AttributeValueData} Values to set on add feature value form inputs
   * @param saveAndStay {boolean} True if we need to save and stay
   * @return {Promise<string>}
   */
  // eslint-disable-next-line consistent-return
  async addEditValue(page: Page, valueData: FeatureValueData, saveAndStay: boolean = false): Promise<string | void> {
    // Set group and value
    if (!(await this.isDisabled(page, this.featureSelect))) {
      await this.selectByVisibleText(page, this.featureSelect, valueData.featureName);
    }
    await this.setValue(page, this.valueInput, valueData.value);

    // Set Url and meta title
    //await this.setValue(page, this.urlInput, valueData.url);
    //await this.setValue(page, this.metaTitleInput, valueData.metaTitle);

    // Save value
    if (saveAndStay) {
      await page.click(this.saveAndStayButton);
    } else {
      await this.clickAndWaitForURL(page, this.saveButton);
    }
    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddValue();
