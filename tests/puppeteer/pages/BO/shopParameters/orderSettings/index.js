require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class OrderSettings extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order settings •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    this.generalForm = '#configuration_form';
    this.enableFinalSummaryLabel = `${this.generalForm} label[for='form_general_enable_final_summary_%TOGGLE']`;
    this.enableGuestCheckoutLabel = `${this.generalForm} label[for='form_general_enable_guest_checkout_%TOGGLE']`;
    this.disableReorderingLabel = `${this.generalForm} label[for='form_general_disable_reordering_option_%TOGGLE']`;
    this.minimumPurchaseRequiredValue = '#form_general_purchase_minimum_value';
    this.enableTermsOfServiceLabel = `${this.generalForm} label[for='form_general_enable_tos_%TOGGLE']`;
    this.pageForTermsAndConditionsSelect = '#form_general_tos_cms_id';
    this.saveGeneralFormButton = `${this.generalForm} .card-footer button`;
    // Gift options form
    this.giftWrappingToogle = `${this.generalForm} label[for='form_gift_options_enable_gift_wrapping_%TOGGLE']`;
    this.giftWrappingPriceInput = '#form_gift_options_gift_wrapping_price';
    this.giftWrappingTaxSelect = '#form_gift_options_gift_wrapping_tax_rules_group';
    this.recycledPackagingToogle = `${this.generalForm} label[for='form_gift_options_offer_recyclable_pack_%TOGGLE']`;
    this.saveGiftOptionsFormButton = `${this.generalForm} div:nth-of-type(2) .card-footer button`;
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
    await this.waitForSelectorAndClick(this.enableFinalSummaryLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable guest checkout
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setGuestCheckoutStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.enableGuestCheckoutLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable reordering option
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setReorderOptionStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.disableReorderingLabel.replace('%TOGGLE', toEnable ? 1 : 0));
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
    await this.waitForSelectorAndClick(this.enableTermsOfServiceLabel.replace('%TOGGLE', toEnable ? 1 : 0));
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
    await this.page.click(this.giftWrappingToogle.replace('%TOGGLE', wantedStatus ? 1 : 0));
    if (wantedStatus) {
      await this.setValue(this.giftWrappingPriceInput, price.toString());
      await this.selectByVisibleText(this.giftWrappingTaxSelect, tax);
    }
    await this.page.click(this.recycledPackagingToogle.replace('%TOGGLE', recyclePackagingStatus ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGiftOptionsFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
