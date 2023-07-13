import BOBasePage from '@pages/BO/BObasePage';
import {Page} from 'playwright';

/**
 * Feature flag page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class FeatureFlag extends BOBasePage {
  public readonly pageTitle: string;

  public readonly featureFlagProductPageV2: string;

  public readonly featureFlagMultipleImageFormats: string;

  public readonly featureFlagAuthorizationServer: string;

  private readonly featureFlagSwitchButton: (status: string, feature: string, toggle: number) => string;

  private readonly submitButton: (status: string) => string;

  private readonly alertSuccess: string;

  private readonly modalSubmitFeatureFlag: string;

  private readonly enableExperimentalfeatureButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on import page
   */
  constructor() {
    super();

    this.pageTitle = `New & experimental features • ${global.INSTALL.SHOP_NAME}`;
    this.successfulUpdateMessage = 'Update successful';

    // Feature Flag
    this.featureFlagProductPageV2 = 'product_page_v2';
    this.featureFlagMultipleImageFormats = 'multiple_image_format';
    this.featureFlagAuthorizationServer = 'authorization_server';
    // Selectors
    this.featureFlagSwitchButton = (status: string, feature: string, toggle: number) => `#feature_flag_${
      status}_feature_flags_${feature}_enabled_${toggle}`;
    this.submitButton = (status: string) => `#feature_flag_${status}_submit`;
    this.alertSuccess = 'div.alert.alert-success[role="alert"]';
    this.modalSubmitFeatureFlag = '#modal-confirm-submit-feature-flag';
    this.enableExperimentalfeatureButton = `${this.modalSubmitFeatureFlag} button.btn-confirm-submit`;
  }

  /**
   * Enable/Disable feature flag
   * @param page {Page} Browser tab
   * @param featureFlag {string}
   * @param toEnable {boolean} True if we need to enable new product page
   * @returns {Promise<string>}
   */
  async setFeatureFlag(page: Page, featureFlag: string, toEnable: boolean = true): Promise<string> {
    let isStable: boolean;

    switch (featureFlag) {
      case this.featureFlagMultipleImageFormats:
        isStable = true;
        break;
      case this.featureFlagProductPageV2:
        isStable = true;
        break;
      case this.featureFlagAuthorizationServer:
        isStable = false;
        break;
      default:
        throw new Error(`The feature flag ${featureFlag} is not defined`);
    }

    const selector: string = this.featureFlagSwitchButton(isStable ? 'stable' : 'beta', featureFlag, toEnable ? 1 : 0);

    const isChecked = await this.isChecked(page, selector);

    if (isChecked) {
      // Return the successful message to simulate all went good (no need to change the value here)
      return this.successfulUpdateMessage;
    }

    await this.setChecked(page, selector);
    await this.waitForSelectorAndClick(page, this.submitButton(isStable ? 'stable' : 'beta'));
    // The confirmation modal is only displayed for experimental/beta feature flags
    if (toEnable && !isStable) {
      await this.waitForVisibleSelector(page, this.modalSubmitFeatureFlag);
      await this.clickAndWaitForURL(page, this.enableExperimentalfeatureButton);
    }

    return this.getTextContent(page, this.alertSuccess);
  }
}

export default new FeatureFlag();
