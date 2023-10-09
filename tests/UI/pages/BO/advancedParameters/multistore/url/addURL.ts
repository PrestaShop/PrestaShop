import BOBasePage from '@pages/BO/BObasePage';

import type ShopData from '@data/faker/shop';

import type {Page} from 'playwright';

/**
 * Add url page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddUrl extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  public readonly errorDisableMainURLMessage: string;

  public readonly ErrorDisableShopMessage: string;

  private readonly domainInput: string;

  private readonly virtualUrlInput: string;

  private readonly mainURLButton: (status: string) => string;

  private readonly enabledButton: (status: string) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add url page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Add new •';
    this.pageTitleEdit = 'Multistore > Edit';
    this.errorDisableMainURLMessage = 'You cannot change a main URL to a non-main URL. You have to set another URL'
      + ' as your Main URL for the selected shop.';
    this.ErrorDisableShopMessage = 'You cannot disable the Main URL.';

    // Selectors
    this.domainInput = '#domain';
    this.virtualUrlInput = '#virtual_uri';
    this.mainURLButton = (status: string) => `#main_${status}`;
    this.enabledButton = (status: string) => `#active_${status}`;
    this.saveButton = '#shop_url_form_submit_btn_1';
  }

  /*
  Methods
   */

  /**
   * Add shop URL
   * @param page {Page} Browser tab
   * @param url {string} url to set on edit/add shop form
   * @returns {Promise<string>}
   */
  async setVirtualUrl(page: Page, url: string): Promise<string> {
    await this.setValue(page, this.virtualUrlInput, url);

    await this.clickAndWaitForURL(page, this.saveButton, 'networkidle', 60000);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Set main URL
   * @param page {Page} Browser tab
   * @param status {string} Main url status
   * @returns {Promise<string>}
   */
  async setMainURL(page: Page, status: string): Promise<string> {
    await this.setChecked(page, this.mainURLButton(status));
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getTextContent(page, this.alertBlock);
  }

  /**
   * Set shop url status
   * @param page {Page} Browser tab
   * @param status {string} shop url status
   * @returns {Promise<string>}
   */
  async setShopStatus(page: Page, status: string): Promise<string> {
    await this.setChecked(page, this.enabledButton(status));
    await this.waitForSelectorAndClick(page, this.saveButton);

    return this.getTextContent(page, this.alertBlock);
  }
}

export default new AddUrl();
