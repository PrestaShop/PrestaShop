require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class orderSettings extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order settings •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    this.generalForm = '#configuration_form';
    this.enableFinalSummaryLabel = 'label[for=\'form_general_enable_final_summary_%TOGGLE\']';
    this.saveGeneralFormButton = `${this.generalForm} .card-footer button`;
  }

  /*
    Methods
  */

  /**
   * Enable/disable final summary
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async setFinalSummary(toEnable = true) {
    await this.waitForSelectorAndClick(this.enableFinalSummaryLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
