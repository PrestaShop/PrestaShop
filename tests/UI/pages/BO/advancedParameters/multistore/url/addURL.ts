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

  private readonly virtualUrlInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add url page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Add new •';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.virtualUrlInput = '#virtual_uri';
    this.saveButton = '#shop_url_form_submit_btn_1';
  }

  /*
  Methods
   */

  /**
   * Add shop URL
   * @param page {Page} Browser tab
   * @param shopData {ShopData} Data to set on edit/add shop form
   * @returns {Promise<string>}
   */
  async setVirtualUrl(page: Page, shopData: ShopData): Promise<string> {
    await this.setValue(page, this.virtualUrlInput, shopData.name);

    await this.clickAndWaitForURL(page, this.saveButton, 'networkidle', 60000);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

export default new AddUrl();
