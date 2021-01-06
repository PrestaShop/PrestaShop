require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Preferences extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Preferences •';
    this.successfulUpdateMessage = 'Update successful';

    // Carrier options selectors
    this.defaultCarrierSelect = '#carrier-options_default_carrier';
    this.sortBySelect = '#carrier-options_carrier_default_order_by';
    this.saveCarrierOptionsButton = '#save-carrier-options-button';
  }

  /* Carrier options methods */

  /**
   * Set default carrier in carrier options form
   * @param page
   * @param carrierName
   * @return {Promise<string>}
   */
  async setDefaultCarrier(page, carrierName) {
    await this.selectByVisibleText(page, this.defaultCarrierSelect, carrierName);

    // Save configuration and return successful message
    await this.clickAndWaitForNavigation(page, this.saveCarrierOptionsButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set carriers sort By 'Price' or 'Position' in carrier option form
   * @param page
   * @param sortBy
   * @returns {Promise<string>}
   */
  async setCarrierSortBy(page, sortBy) {
    await this.selectByVisibleText(page, this.sortBySelect, sortBy);

    // Save configuration and return successful message
    await this.clickAndWaitForNavigation(page, this.saveCarrierOptionsButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

module.exports = new Preferences();
