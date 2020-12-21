require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class OrderSettings extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Order settings •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    // SubTab
    this.statusesTab = '#subtab-AdminStatuses';
    // Form
    this.generalForm = '#configuration_form';
    this.enableFinalSummaryLabel = toggle => `${this.generalForm}`
      + ` label[for='form_general_enable_final_summary_${toggle}']`;
    this.enableGuestCheckoutLabel = toggle => `${this.generalForm}`
      + ` label[for='form_general_enable_guest_checkout_${toggle}']`;
    this.disableReorderingLabel = toggle => `${this.generalForm}`
      + ` label[for='form_general_disable_reordering_option_${toggle}']`;
    this.minimumPurchaseRequiredValue = '#form_general_purchase_minimum_value';
    this.enableTermsOfServiceLabel = toggle => `${this.generalForm} label[for='form_general_enable_tos_${toggle}']`;
    this.pageForTermsAndConditionsSelect = '#form_general_tos_cms_id';
    this.saveGeneralFormButton = `${this.generalForm} .card-footer button`;
    // Gift options form
    this.giftWrappingToggle = toggle => `${this.generalForm}`
      + ` label[for='form_gift_options_enable_gift_wrapping_${toggle}']`;
    this.giftWrappingPriceInput = '#form_gift_options_gift_wrapping_price';
    this.giftWrappingTaxSelect = '#form_gift_options_gift_wrapping_tax_rules_group';
    this.recycledPackagingToggle = toggle => `${this.generalForm}`
      + ` label[for='form_gift_options_offer_recyclable_pack_${toggle}']`;
    this.saveGiftOptionsFormButton = `${this.generalForm} div:nth-of-type(2) .card-footer button`;
  }

  /*
    Methods
  */

  /**
   * Enable/disable final summary
   * @param page
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async setFinalSummaryStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.enableFinalSummaryLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable guest checkout
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setGuestCheckoutStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.enableGuestCheckoutLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable reordering option
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setReorderOptionStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.disableReorderingLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set minimum quantity required to purchase
   * @param page
   * @param value
   * @returns {Promise<string>}
   */
  async setMinimumPurchaseRequiredTotal(page, value) {
    await this.setValue(page, this.minimumPurchaseRequiredValue, value.toString());
    await this.clickAndWaitForNavigation(page, this.saveGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set terms of service
   * @param page
   * @param toEnable
   * @param pageName
   * @returns {Promise<string>}
   */
  async setTermsOfService(page, toEnable = true, pageName = '') {
    await this.waitForSelectorAndClick(page, this.enableTermsOfServiceLabel(toEnable ? 1 : 0));
    if (toEnable) {
      await this.selectByVisibleText(page, this.pageForTermsAndConditionsSelect, pageName);
    }
    await this.clickAndWaitForNavigation(page, this.saveGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set gift options form
   * @param page
   * @param wantedStatus
   * @param price
   * @param tax
   * @param recyclePackagingStatus
   * @return {Promise<string>}
   */
  async setGiftOptions(page, wantedStatus = false, price = 0, tax = 'none', recyclePackagingStatus = false) {
    await page.click(this.giftWrappingToggle(wantedStatus ? 1 : 0));
    if (wantedStatus) {
      await this.setValue(page, this.giftWrappingPriceInput, price.toString());
      await this.selectByVisibleText(page, this.giftWrappingTaxSelect, tax);
    }

    await page.click(this.recycledPackagingToggle(recyclePackagingStatus ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveGiftOptionsFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Go to statuses page
   * @param page
   * @returns {Promise<void>}
   */
  async goToStatusesPage(page) {
    await this.clickAndWaitForNavigation(page, this.statusesTab);
  }
}

module.exports = new OrderSettings();
