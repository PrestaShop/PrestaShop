require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class CartRules extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Cart Rules •';

    // Selectors
    this.catalogPriceRulesTab = '#subtab-AdminSpecificPriceRule';
  }

  /* Methods */
  /**
   * Change Tab to Catalog Price Rules in Discounts Page
   * @returns {Promise<void>}
   */
  async goToCatalogPriceRulesTab() {
    await this.page.click(this.catalogPriceRulesTab);
    await this.page.waitForSelector(`${this.catalogPriceRulesTab}.active`, {visible: true});
  }
};
