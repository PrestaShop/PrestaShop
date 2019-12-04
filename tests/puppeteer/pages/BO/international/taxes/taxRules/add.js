require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddTaxRules extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Taxes •';

    // Selectors
    this.nameInput = '#name';
    this.enabledSwitchlabel = 'label[for=\'active_%ID\']';
    this.saveTaxButton = '#tax_rules_group_form_submit_btn';
  }
  /*
  Methods
   */

  /**
   * Fill form for add/edit tax rules group
   * @param taxRuleGroupData
   * @return {Promise<textContent>}
   */
  async createEditTaxRulesGroup(taxRuleGroupData) {
    await this.setValue(this.nameInput, taxRuleGroupData.name);
    if (taxRuleGroupData.enabled) {
      await this.page.click(this.enabledSwitchlabel.replace('%ID', '1'));
    } else {
      await this.page.click(this.enabledSwitchlabel.replace('%ID', '0'));
    }
    // Save Tax rules group
    await this.clickAndWaitForNavigation(this.saveTaxButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
