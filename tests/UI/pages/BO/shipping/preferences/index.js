require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Preferences extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Preferences •';
    this.successfulUpdateMessage = 'Update successful';

    // Handling form selectors
    this.handlingForm = '#handling';
    this.handlingChargesInput = '#handling_shipping_handling_charges';
    this.saveHandlingButton = `${this.handlingForm} button`;

    // Carrier options selectors
    this.carrierOptionForm = '#carrier-options';
    this.defaultCarrierSelect = '#carrier-options_default_carrier';
    this.sortBySelect = '#carrier-options_carrier_default_order_by';
    this.orderBySelect = '#carrier-options_carrier_default_order_way';
    this.saveCarrierOptionsButton = `${this.carrierOptionForm} button`;
  }

  /* Handling methods */

  /**
   * Set handling charges button
   * @param page {Page} Browser tab
   * @param value {String} The handling charges value
   * @returns {Promise<string>}
   */
  async setHandlingCharges(page, value) {
    await this.setValue(page, this.handlingChargesInput, value);

    // Save handling form and return successful message
    await this.clickAndWaitForNavigation(page, this.saveHandlingButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Carrier options methods */

  /**
   * Set default carrier in carrier options form
   * @param page {Page} Browser tab
   * @param carrierName {String} The carrier name
   * @return {Promise<string>}
   */
  async setDefaultCarrier(page, carrierName) {
    await this.selectByVisibleText(page, this.defaultCarrierSelect, carrierName);

    // Save configuration and return successful message
    await this.clickAndWaitForNavigation(page, this.saveCarrierOptionsButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set carriers sort By 'Price' or 'Position' / order by 'Ascending' or 'descending' in carrier options form
   * @param page {Page} Browser tab
   * @param sortBy {String} Sort by 'Price' or 'Position'
   * @param orderBy {String} Order by 'Ascending' or 'Descending'
   * @returns {Promise<string>}
   */
  async setCarrierSortOrderBy(page, sortBy, orderBy = 'Ascending') {
    await this.selectByVisibleText(page, this.sortBySelect, sortBy);
    await this.selectByVisibleText(page, this.orderBySelect, orderBy);

    // Save configuration and return successful message
    await this.clickAndWaitForNavigation(page, this.saveCarrierOptionsButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new Preferences();
