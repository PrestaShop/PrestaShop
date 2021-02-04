require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddUrl extends BOBasePage {
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
   * @param page
   * @param shopData
   * @returns {Promise<*>}
   */
  async setVirtualUrl(page, shopData) {
    await this.setValue(page, this.virtualUrlInput, shopData.name);

    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new AddUrl();
