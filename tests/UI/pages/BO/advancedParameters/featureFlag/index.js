require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Feature flag page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class FeatureFlag extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on import page
   */
  constructor() {
    super();

    this.pageTitle = 'New & Experimental Features • PrestaShop';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    this.newProductPageSwitchButton = toggle => `#feature_flag_beta_feature_flags_product_page_v2_enabled_${toggle}`;
    this.submitButton = '#feature_flag_beta_submit';
    this.alertSuccess = 'div.alert.alert-success[role="alert"]';
    this.modalSubmitFeatureFlag = '#modal-confirm-submit-feature-flag';
    this.enableExperimentalfeatureButton = `${this.modalSubmitFeatureFlag} button.btn-confirm-submit`;
  }

  /*
  Methods
   */

  /**
   * Enable/Disable new product page
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable new product page
   * @returns {Promise<void>}
   */
  async setNewProductPage(page, toEnable = true) {
    await this.setChecked(page, this.newProductPageSwitchButton(toEnable ? 1 : 0));
    await this.waitForSelectorAndClick(page, this.submitButton);
    if (toEnable) {
      await this.waitForVisibleSelector(page, this.modalSubmitFeatureFlag);
      await this.clickAndWaitForNavigation(page, this.enableExperimentalfeatureButton);
    }

    return this.getTextContent(page, this.alertSuccess);
  }
}

module.exports = new FeatureFlag();
