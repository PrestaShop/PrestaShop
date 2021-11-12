require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add url page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddUrl extends BOBasePage {
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
  async setVirtualUrl(page, shopData) {
    await this.setValue(page, this.virtualUrlInput, shopData.name);

    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new AddUrl();
