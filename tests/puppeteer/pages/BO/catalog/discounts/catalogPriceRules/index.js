require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class CatalogPriceRules extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Catalog Price Rules •';

    // Selectors header
    this.addNewCatalogPriceRuleButton = '#page-header-desc-specific_price_rule-new_specific_price_rule';
  }

  /* Methods */
  /**
   * Go to add new Catalog price rule page
   * @returns {Promise<void>}
   */
  async goToAddNewCatalogPriceRulePage() {
    await this.clickAndWaitForNavigation(this.addNewCatalogPriceRuleButton);
  }
};
