import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Administration page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AdministrationPage extends BOBasePage {
  public readonly pageTitle: string;

  public readonly dangerAlertCookieSameSite: string;

  private readonly cookiesIpAddressRadioButton: (status: string) => string;

  private readonly lifeTimeFOCookies: string;

  private readonly lifeTimeBOCookies: string;

  private readonly generalCookieSameSite: string;

  private readonly generalFormSaveButton: string;

  private readonly maxSizeAttachedFiles: string;

  private readonly maxSizeDownloadableProduct: string;

  private readonly maxSizeProductImage: string;

  private readonly saveUploadQuotaForm: string;

  private readonly notificationsForNewOrdersSwitchButton: (toEnable: number) => string;

  private readonly notificationsForNewCustomersSwitchButton: (toEnable: number) => string;

  private readonly notificationsForNewMessagesSwitchButton: (toEnable: number) => string;

  private readonly notificationsFormSaveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Performance page
   */
  constructor() {
    super();

    this.pageTitle = `Administration • ${global.INSTALL.SHOP_NAME}`;
    this.successfulUpdateMessage = 'Update successful';
    this.dangerAlertCookieSameSite = 'The SameSite=None attribute is only available in secure mode.';

    // Selectors
    // General form
    this.cookiesIpAddressRadioButton = (status: string) => `#general_check_ip_address_${status}`;
    this.lifeTimeFOCookies = '#general_front_cookie_lifetime';
    this.lifeTimeBOCookies = '#general_back_cookie_lifetime';
    this.generalCookieSameSite = '#general_cookie_samesite';
    this.generalFormSaveButton = '#configuration_fieldset_general button';
    this.alertSuccessBlock = 'div.alert[role=alert] div.alert-text';

    // Notifications form selectors
    this.notificationsForNewOrdersSwitchButton = (toEnable: number) => `#notifications_show_notifs_new_orders_${toEnable}`;
    this.notificationsForNewCustomersSwitchButton = (toEnable: number) => `#notifications_show_notifs_new_customers_${toEnable}`;
    this.notificationsForNewMessagesSwitchButton = (toEnable: number) => `#notifications_show_notifs_new_messages_${toEnable}`;
    this.notificationsFormSaveButton = '#configuration_fieldset_notifications button';

    // Upload quota form selectors
    this.maxSizeAttachedFiles = '#upload-quota_max_size_attached_files';
    this.maxSizeDownloadableProduct = '#upload-quota_max_size_downloadable_product';
    this.maxSizeProductImage = '#upload-quota_max_size_product_image';
    this.saveUploadQuotaForm = '#configuration_fieldset_upload button';
  }

  /*
  Methods
   */
  /**
   * Is check cookies enabled
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isCheckCookiesAddressEnabled(page: Page): Promise<boolean> {
    return this.isChecked(page, `${this.cookiesIpAddressRadioButton('1')}`);
  }

  /**
   * Set cookies ip address
   * @param page {Page} Browser tab
   * @param toEnable {Promise<string>}
   * @return {Promise<void>}
   */
  async setCookiesIPAddress(page: Page, toEnable: boolean = true): Promise<void> {
    await this.setChecked(page, this.cookiesIpAddressRadioButton(toEnable ? '1' : '0'));
  }

  /**
   * Sel lifeTime FO cookies
   * @param page {Page} Browser tab
   * @param value {number} Number to set on lifetime FO cookies
   * @return {Promise<void>}
   */
  async setLifetimeFOCookies(page: Page, value: number): Promise<void> {
    await this.setValue(page, this.lifeTimeFOCookies, value);
  }

  /**
   * Sel lifeTime BO cookies
   * @param page {Page} Browser tab
   * @param value {number} Number to set on lifetime BO cookies
   * @return {Promise<void>}
   */
  async setLifetimeBOCookies(page: Page, value: number): Promise<void> {
    await this.setValue(page, this.lifeTimeBOCookies, value);
  }

  /**
   * Select cookie same site
   * @param page {Page} Browser tab
   * @param value {string} Value to set on cookie same site
   * @return {Promise<void>}
   */
  async setCookieSameSite(page: Page, value: string): Promise<void> {
    await this.selectByVisibleText(page, this.generalCookieSameSite, value);
  }

  /**
   * Save general form
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async saveGeneralForm(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.generalFormSaveButton);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Enable/Disable show notifications for new orders
   * @param page {Page} Browser tab
   * @param toEnable {string} True if we need to enable notifications for new orders
   * @return {Promise<string>}
   */
  async setShowNotificationsForNewOrders(page: Page, toEnable: boolean): Promise<string> {
    await this.setChecked(page, this.notificationsForNewOrdersSwitchButton(toEnable ? 1 : 0));
    await this.clickAndWaitForURL(page, this.notificationsFormSaveButton);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Enable/Disable show notifications for new customers
   * @param page {Page} Browser tab
   * @param toEnable {string} True if we need to enable notifications for new customers
   * @return {Promise<string>}
   */
  async setShowNotificationsForNewCustomers(page: Page, toEnable: boolean): Promise<string> {
    await this.setChecked(page, this.notificationsForNewCustomersSwitchButton(toEnable ? 1 : 0));
    await this.clickAndWaitForURL(page, this.notificationsFormSaveButton);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Enable/Disable show notifications for new messages
   * @param page {Page} Browser tab
   * @param toEnable {string} True if we need to enable notifications for new messages
   * @return {Promise<string>}
   */
  async setShowNotificationsForNewMessages(page: Page, toEnable: boolean): Promise<string> {
    await this.setChecked(page, this.notificationsForNewMessagesSwitchButton(toEnable ? 1 : 0));
    await this.clickAndWaitForURL(page, this.notificationsFormSaveButton);

    return this.getAlertSuccessBlockContent(page);
  }

  // Methods for upload quota form
  /**
   * Set max size for attached files
   * @param page {Page} Browser tab
   * @param size {number} The data to set in size input
   * @return {Promise<string>}
   */
  async setMaxSizeAttachedFiles(page: Page, size: number): Promise<string> {
    await this.setValue(page, this.maxSizeAttachedFiles, size);
    await page.locator(this.saveUploadQuotaForm).click();

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Set max size for downloaded product image
   * @param page {Page} Browser tab
   * @param size {number} The data to set in size input
   * @return {Promise<string>}
   */
  async setMaxSizeDownloadedProduct(page: Page, size: number): Promise<string> {
    await this.setValue(page, this.maxSizeDownloadableProduct, size);
    await page.locator(this.saveUploadQuotaForm).click();

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Set max size for product image
   * @param page {Page} Browser tab
   * @param size {number} The data to set in size input
   * @return {Promise<string>}
   */
  async setMaxSizeForProductImage(page: Page, size: number): Promise<string> {
    await this.setValue(page, this.maxSizeProductImage, size);
    await page.locator(this.saveUploadQuotaForm).click();

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new AdministrationPage();
