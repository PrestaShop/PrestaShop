import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Order settings page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class OrderSettings extends BOBasePage {
  public readonly pageTitle: string;

  private readonly statusesTab: string;

  private readonly generalForm: string;

  private readonly enableFinalSummaryToggleInput: (toggle: number) => string;

  private readonly enableGuestCheckoutToggleInput: (toggle: number) => string;

  private readonly disableReorderingToggleInput: (toggle: number) => string;

  private readonly minimumPurchaseRequiredValue: string;

  private readonly recalculateShippingCostAfterEditOrder: (toggle: number) => string;

  private readonly enableTermsOfServiceToggleInput: (toggle: number) => string;

  private readonly pageForTermsAndConditionsSelect: string;

  private readonly saveGeneralFormButton: string;

  private readonly giftForm: string;

  private readonly giftWrappingToggleInput: (toggle: number) => string;

  private readonly giftWrappingPriceInput: string;

  private readonly giftWrappingTaxSelect: string;

  private readonly recycledPackagingToggleInput: (toggle: number) => string;

  private readonly saveGiftOptionsFormButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on order settings page
   */
  constructor() {
    super();

    this.pageTitle = 'Order settings •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    // SubTab
    this.statusesTab = '#subtab-AdminStatuses';
    // Form
    this.generalForm = '#configuration_general_form';
    this.enableFinalSummaryToggleInput = (toggle: number) => `#general_enable_final_summary_${toggle}`;
    this.enableGuestCheckoutToggleInput = (toggle: number) => `#general_enable_guest_checkout_${toggle}`;
    this.disableReorderingToggleInput = (toggle: number) => `#general_disable_reordering_option_${toggle}`;
    this.minimumPurchaseRequiredValue = '#general_purchase_minimum_value';
    this.recalculateShippingCostAfterEditOrder = (toggle: number) => `#general_recalculate_shipping_cost_${toggle}`;
    this.enableTermsOfServiceToggleInput = (toggle: number) => `#general_enable_tos_${toggle}`;
    this.pageForTermsAndConditionsSelect = '#general_tos_cms_id';
    this.saveGeneralFormButton = `${this.generalForm} #form-general-save-button`;

    // Gift options form
    this.giftForm = '#configuration_gift_options_form';
    this.giftWrappingToggleInput = (toggle: number) => `#gift_options_enable_gift_wrapping_${toggle}`;
    this.giftWrappingPriceInput = '#gift_options_gift_wrapping_price';
    this.giftWrappingTaxSelect = '#gift_options_gift_wrapping_tax_rules_group';
    this.recycledPackagingToggleInput = (toggle: number) => `#gift_options_offer_recyclable_pack_${toggle}`;
    this.saveGiftOptionsFormButton = `${this.giftForm} #form-gift-save-button`;
  }

  /*
    Methods
  */

  /**
   * Enable/disable final summary
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable final summary status
   * @return {Promise<string>}
   */
  async setFinalSummaryStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.enableFinalSummaryToggleInput(toEnable ? 1 : 0));
    await page.locator(this.saveGeneralFormButton).click();
    await this.elementNotVisible(page, this.enableFinalSummaryToggleInput(!toEnable ? 1 : 0), 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable guest checkout
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable guest checkout status
   * @returns {Promise<string>}
   */
  async setGuestCheckoutStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.enableGuestCheckoutToggleInput(toEnable ? 1 : 0));
    await page.locator(this.saveGeneralFormButton).click();
    await this.elementNotVisible(page, this.enableGuestCheckoutToggleInput(!toEnable ? 1 : 0), 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable reordering option
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable reordering option status
   * @returns {Promise<string>}
   */
  async setReorderOptionStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.disableReorderingToggleInput(toEnable ? 1 : 0));
    await page.locator(this.saveGeneralFormButton).click();
    await this.elementNotVisible(page, this.disableReorderingToggleInput(!toEnable ? 1 : 0), 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set minimum quantity required to purchase
   * @param page {Page} Browser tab
   * @param value {number} Value to set on minimum purchase input
   * @returns {Promise<string>}
   */
  async setMinimumPurchaseRequiredTotal(page: Page, value: number): Promise<string> {
    await this.setValue(page, this.minimumPurchaseRequiredValue, value.toString());
    await page.locator(this.saveGeneralFormButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Recalculate shipping cost after editing order
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable recalculate shipping cost
   * @returns {Promise<string>}
   */
  async recalculateShippingCostAfterEditingOrder(page: Page, toEnable: boolean): Promise<string> {
    await this.setChecked(page, this.recalculateShippingCostAfterEditOrder(toEnable ? 1 : 0));
    await page.locator(this.saveGeneralFormButton).click();
    await this.elementNotVisible(page, this.recalculateShippingCostAfterEditOrder(!toEnable ? 1 : 0), 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set terms of service
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable terms of service
   * @param pageName {string} Page name to choose
   * @returns {Promise<string>}
   */
  async setTermsOfService(page: Page, toEnable: boolean = true, pageName: string = ''): Promise<string> {
    await this.setChecked(page, this.enableTermsOfServiceToggleInput(toEnable ? 1 : 0));
    if (toEnable) {
      await this.selectByVisibleText(page, this.pageForTermsAndConditionsSelect, pageName);
    }
    await page.locator(this.saveGeneralFormButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set gift options form
   * @param page {Page} Browser tab
   * @param wantedStatus {boolean}  True if we need to enable gift wrapping status
   * @param price {number} Price to set on gift wrapping price input
   * @param tax {string} Value of tax to select
   * @param recyclePackagingStatus {boolean} True if we need to enable recycle packaging status
   * @return {Promise<string>}
   */
  async setGiftOptions(
    page: Page,
    wantedStatus: boolean = false,
    price: number = 0,
    tax: string = 'none',
    recyclePackagingStatus: boolean = false,
  ): Promise<string> {
    await this.setChecked(page, this.giftWrappingToggleInput(wantedStatus ? 1 : 0));
    if (wantedStatus) {
      await this.setValue(page, this.giftWrappingPriceInput, price);
      await this.selectByVisibleText(page, this.giftWrappingTaxSelect, tax);
    }

    await this.setChecked(page, this.recycledPackagingToggleInput(recyclePackagingStatus ? 1 : 0));
    await page.locator(this.saveGiftOptionsFormButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Go to statuses page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToStatusesPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.statusesTab);
  }
}

export default new OrderSettings();
