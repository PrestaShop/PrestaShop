require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class CartRules extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Cart Rules •';

    // Selectors
    this.catalogPriceRulesTab = '#subtab-AdminSpecificPriceRule';
  }

  /* Methods */
  /**
   * Change Tab to Catalog Price Rules in Discounts Page
   * @param page
   * @returns {Promise<void>}
   */
  async goToCatalogPriceRulesTab(page) {
    await this.clickAndWaitForNavigation(page, this.catalogPriceRulesTab);
    await this.waitForVisibleSelector(page, `${this.catalogPriceRulesTab}.current`);
  }
}

module.exports = new CartRules();
