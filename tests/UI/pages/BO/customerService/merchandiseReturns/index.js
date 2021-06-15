require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Merchandise returns page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class MerchandiseReturns extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on merchandise return page
   */
  constructor() {
    super();

    this.pageTitle = 'Merchandise Returns •';
    this.successfulUpdateMessage = 'The settings have been successfully updated.';

    // Selectors
    // Options
    this.generalForm = '#order_return_fieldset_general';
    this.enableOrderReturnLabel = toggle => `${this.generalForm} #PS_ORDER_RETURN_${toggle}`;
    this.returnsPrefixInput = '#conf_id_PS_RETURN_PREFIX input[name=\'PS_RETURN_PREFIX_1\']';
    this.saveButton = `${this.generalForm} button[name='submitOptionsorder_return']`;
  }

  /*
    Methods
  */

  /**
   * Enable/Disable merchandise returns
   * @param page {Page} Browser tab
   * @param status {boolean} Status to set on the order return
   * @returns {Promise<string>}
   */
  async setOrderReturnStatus(page, status = true) {
    await page.check(this.enableOrderReturnLabel(status ? 'on' : 'off'));
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Update returns prefix
   * @param page {Page} Browser tab
   * @param prefix {string} Value of prefix to set on return prefix input
   * @returns {Promise<string>}
   */
  async setReturnsPrefix(page, prefix) {
    await this.setValue(page, this.returnsPrefixInput, prefix);
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new MerchandiseReturns();
