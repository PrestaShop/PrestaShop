require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class MerchandiseReturns extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Merchandise Returns •';
    this.successfulUpdateMessage = 'The settings have been successfully updated.';

    // Selectors
    // Options
    this.generalForm = '#order_return_fieldset_general';
    this.enableOrderReturnLabel = toggle => `${this.generalForm} label[for='PS_ORDER_RETURN_${toggle}']`;
    this.saveButton = `${this.generalForm} button[name='submitOptionsorder_return']`;
  }

  /*
    Methods
  */

  /**
   * Enable/Disable merchandise returns
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setOrderReturnStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.enableOrderReturnLabel(toEnable ? 'on' : 'off'));
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new MerchandiseReturns();
