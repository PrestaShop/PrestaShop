import LocalizationBasePage from '@pages/BO/international/localization/localizationBasePage';

import type {Page} from 'playwright';

/**
 * Geolocation page, contains functions that can be used on the page
 * @class
 * @extends LocalizationBasePage
 */
class GeolocationPage extends LocalizationBasePage {
  public readonly pageTitle: string;

  public readonly messageWarningNeedDB: string;

  public readonly messageGeolocationDBUnavailable: string;

  private readonly warningMessage: string;

  private readonly geolocationByIPAddressForm: string;

  private readonly golocationByIPAddressInput: (status: boolean) => string;

  private readonly geolocationByIPAddressSaveButton: string;

  private readonly ipAddressWhiteForm: string;

  private readonly golocationByIPAddressTextarea: string;

  private readonly ipAddressWhiteSaveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on localization page
   */
  constructor() {
    super();

    this.successfulUpdateMessage = 'Update successful';

    this.pageTitle = `Geolocation • ${global.INSTALL.SHOP_NAME}`;
    this.messageWarningNeedDB = 'Since December 30, 2019, you need to register for a MaxMind account to get a license key to be'
      + ' able to download the geolocation data. Once downloaded, extract the data using Winrar or Gzip into the '
      + '/app/Resources/geoip/ directory.';
    this.messageGeolocationDBUnavailable = 'The geolocation database is unavailable.';
    this.warningMessage = 'div#main-div div.alert-warning span.alert-text';

    // Geolocation By IP Address
    this.geolocationByIPAddressForm = 'form[name="geolocation_by_ip_address"]';
    this.golocationByIPAddressInput = (status: boolean) => `${this.geolocationByIPAddressForm} `
        + `#geolocation_by_ip_address_geolocation_enabled_${status ? 1 : 0}`;
    this.geolocationByIPAddressSaveButton = `${this.geolocationByIPAddressForm} .card-footer button`;

    // IP address whitelist
    this.ipAddressWhiteForm = 'form[name="geolocation_whitelist"]';
    this.golocationByIPAddressTextarea = `${this.ipAddressWhiteForm} #geolocation_whitelist_geolocation_whitelist`;
    this.ipAddressWhiteSaveButton = `${this.ipAddressWhiteForm} .card-footer button`;
  }

  /* Methods */
  /**
   * Enable/Disable the Geolocation By IP Address
   * @param page {Page} Browser tab
   * @param status {boolean} Enable/Disable
   * @return {Promise<void>}
   */
  async setGeolocationByIPAddressStatus(page: Page, status: boolean): Promise<void> {
    await this.setChecked(page, this.golocationByIPAddressInput(status), true);
  }

  /**
   * Save the form Geolocation By IP Address
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async saveFormGeolocationByIPAddress(page: Page): Promise<string> {
    await page.locator(this.geolocationByIPAddressSaveButton).click();

    return this.getAlertBlockContent(page);
  }

  /**
   * Get the whitelisted IP Addresses list
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getWhiteListedIPAddresses(page: Page): Promise<string> {
    return this.getTextContent(page, this.golocationByIPAddressTextarea, true, false);
  }

  /**
   * Set the whitelisted IP Addresses list
   * @param page {Page} Browser tab
   * @param whitelist {string} The whitelist
   * @return {Promise<void>}
   */
  async setWhiteListedIPAddresses(page: Page, whitelist: string): Promise<void> {
    await this.setValue(page, this.golocationByIPAddressTextarea, whitelist);
  }

  /**
   * Save the form IP Address Whitelist
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async saveFormIPAddressesWhitelist(page: Page): Promise<string> {
    await page.locator(this.ipAddressWhiteSaveButton).click();

    return this.getAlertBlockContent(page);
  }

  /**
   * Get the warning message
   * @param page {Page} browser tab
   * @returns {Promise<string>}
   */
  async getWarningMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.warningMessage);
  }
}

export default new GeolocationPage();
