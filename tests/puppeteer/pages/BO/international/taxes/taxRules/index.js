require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class TaxRules extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Tax Rules •';

    // Selectors
    // HEADER buttons
    this.addNewTaxRulesGroupLink = 'a#page-header-desc-tax_rules_group-new_tax_rules_group';
  }

  /*
  Methods
   */

  /**
   * Go to add tax Rules group Page
   * @return {Promise<void>}
   */
  async goToAddNewTaxRulesGroupPage() {
    await this.clickAndWaitForNavigation(this.addNewTaxRulesGroupLink);
  }
};
