require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class TaxRules extends BOBasePage {
  constructor() {
    super();

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
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewTaxRulesGroupPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewTaxRulesGroupLink);
  }
}
module.exports = new TaxRules();
