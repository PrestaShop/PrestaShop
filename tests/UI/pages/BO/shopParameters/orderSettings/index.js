require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class OrderSettings extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order settings •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    this.generalForm = '#configuration_general_form';
    this.enableFinalSummaryLabel = toggle => `${this.generalForm}`
      + ` label[for='general_enable_final_summary_${toggle}']`;
    this.enableGuestCheckoutLabel = toggle => `${this.generalForm}`
      + ` label[for='general_enable_guest_checkout_${toggle}']`;
    this.disableReorderingLabel = toggle => `${this.generalForm}`
      + ` label[for='general_disable_reordering_option_${toggle}']`;
    this.minimumPurchaseRequiredValue = '#general_purchase_minimum_value';
    this.enableTermsOfServiceLabel = toggle => `${this.generalForm} label[for='general_enable_tos_${toggle}']`;
    this.pageForTermsAndConditionsSelect = '#general_tos_cms_id';
    this.saveGeneralFormButton = `${this.generalForm} #form-general-save-button`;
    // Gift options form
    this.giftForm = '#configuration_gift_options_form';
    this.giftWrappingToggle = toggle => `${this.giftForm}`
      + ` label[for='gift_options_enable_gift_wrapping_${toggle}']`;
    this.giftWrappingPriceInput = '#gift_options_gift_wrapping_price';
    this.giftWrappingTaxSelect = '#gift_options_gift_wrapping_tax_rules_group';
    this.recycledPackagingToggle = toggle => `${this.giftForm}`
    + ` label[for='gift_options_offer_recyclable_pack_${toggle}']`;
    this.saveGiftOptionsFormButton = `${this.giftForm} #form-gift-save-button`;
  }

  /*
    Methods
  */

  /**
   * Enable/disable final summary
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async setFinalSummaryStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.enableFinalSummaryLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable guest checkout
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setGuestCheckoutStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.enableGuestCheckoutLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable reordering option
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setReorderOptionStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.disableReorderingLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set minimum quantity required to purchase
   * @param value
   * @returns {Promise<string>}
   */
  async setMinimumPurchaseRequiredTotal(value) {
    await this.setValue(this.minimumPurchaseRequiredValue, value.toString());
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set terms of service
   * @param toEnable
   * @param pageName
   * @returns {Promise<string>}
   */
  async setTermsOfService(toEnable = true, pageName = '') {
    await this.waitForSelectorAndClick(this.enableTermsOfServiceLabel(toEnable ? 1 : 0));
    if (toEnable) {
      await this.selectByVisibleText(this.pageForTermsAndConditionsSelect, pageName);
    }
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set gift options form
   * @param wantedStatus
   * @param price
   * @param tax
   * @param recyclePackagingStatus
   * @return {Promise<string>}
   */
  async setGiftOptions(wantedStatus = false, price = 0, tax = 'none', recyclePackagingStatus = false) {
    await this.page.click(this.giftWrappingToggle(wantedStatus ? 1 : 0));
    if (wantedStatus) {
      await this.setValue(this.giftWrappingPriceInput, price.toString());
      await this.selectByVisibleText(this.giftWrappingTaxSelect, tax);
    }
    await this.page.click(this.recycledPackagingToggle(recyclePackagingStatus ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGiftOptionsFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
