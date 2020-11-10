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
    this.generalForm = '#configuration_general_form';
    this.enableFinalSummaryToggleInput = toggle => `#general_enable_final_summary_${toggle}`;
    this.enableGuestCheckoutToggleInput = toggle => `#general_enable_guest_checkout_${toggle}`;
    this.disableReorderingToggleInput = toggle => `#general_disable_reordering_option_${toggle}`;
    this.minimumPurchaseRequiredValue = '#general_purchase_minimum_value';
    this.enableTermsOfServiceToggleInput = toggle => `#general_enable_tos_${toggle}`;
    this.pageForTermsAndConditionsSelect = '#general_tos_cms_id';
    this.saveGeneralFormButton = `${this.generalForm} #form-general-save-button`;
    // Gift options form
    this.giftForm = '#configuration_gift_options_form';
    this.giftWrappingToggleInput = toggle => `#gift_options_enable_gift_wrapping_${toggle}`;
    this.giftWrappingPriceInput = '#gift_options_gift_wrapping_price';
    this.giftWrappingTaxSelect = '#gift_options_gift_wrapping_tax_rules_group';
    this.recycledPackagingToggleInput = toggle => `#gift_options_offer_recyclable_pack_${toggle}`;
    this.saveGiftOptionsFormButton = `${this.giftForm} #form-gift-save-button`;
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
    await page.check(this.enableFinalSummaryToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveGeneralFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Enable/Disable guest checkout
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setGuestCheckoutStatus(page, toEnable = true) {
    await page.check(this.enableGuestCheckoutToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveGeneralFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Enable/Disable reordering option
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setReorderOptionStatus(page, toEnable = true) {
    await page.check(this.disableReorderingToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveGeneralFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
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
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Set terms of service
   * @param page
   * @param toEnable
   * @param pageName
   * @returns {Promise<string>}
   */
  async setTermsOfService(page, toEnable = true, pageName = '') {
    await page.check(this.enableTermsOfServiceToggleInput(toEnable ? 1 : 0));
    if (toEnable) {
      await this.selectByVisibleText(page, this.pageForTermsAndConditionsSelect, pageName);
    }
    await this.clickAndWaitForNavigation(page, this.saveGeneralFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
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
    await page.check(this.giftWrappingToggleInput(wantedStatus ? 1 : 0));
    if (wantedStatus) {
      await this.setValue(page, this.giftWrappingPriceInput, price.toString());
      await this.selectByVisibleText(page, this.giftWrappingTaxSelect, tax);
    }

    await page.check(this.recycledPackagingToggleInput(recyclePackagingStatus ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveGiftOptionsFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
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
