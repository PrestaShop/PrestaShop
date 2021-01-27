require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Preferences extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Preferences •';
    this.successfulUpdateMessage = 'Update successful';

    // Handling form selectors
    this.handlingForm = '#handling';
    this.handlingChargesInput = '#form_handling_shipping_handling_charges';
    this.saveHandlingButton = `${this.handlingForm} button`;

    // Carrier options selectors
    this.carrierOptionForm = '#carrier-options';
    this.defaultCarrierSelect = '#form_carrier_options_default_carrier';
    this.sortBySelect = '#form_carrier_options_carrier_default_order_by';
    this.orderBySelect = '#form_carrier_options_carrier_default_order_way';
    this.saveCarrierOptionsButton = `${this.carrierOptionForm} button`;
  }

  /* Handling methods */

  /**
   * Set handling charges button
   * @param page
   * @param value
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
   * Set carriers sort By 'Price' or 'Position' / order by 'Ascending' or 'descending' in carrier options form
   * @param page
   * @param sortBy
   * @param orderBy
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
