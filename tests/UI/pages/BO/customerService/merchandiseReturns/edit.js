require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Edit merchandise returns page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class EditMerchandiseReturns extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on edit merchandise return page
   */
  constructor() {
    super();

    this.pageTitle = 'Merchandise Returns > Edit •';

    // Selectors
    this.status = '#state';
    this.saveButton = '#order_return_form_submit_btn';
  }

  /*
    Methods
  */
  /**
   * Set merchandise return status
   * @param page {Page} Browser tab
   * @param status {string} Status to select
   * @returns {Promise<string>}
   */
  async setStatus(page, status) {
    await this.selectByVisibleText(page, this.status, status);
    await this.waitForSelectorAndClick(page, this.saveButton);

    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new EditMerchandiseReturns();
