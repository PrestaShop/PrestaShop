require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class CartRules extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Cart Rules •';

    // Selectors
    this.addNewCartRuleButton = '#page-header-desc-cart_rule-new_cart_rule';
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

  /**
   * Go to add new cart rule page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewCartRulesPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewCartRuleButton);
  }

  async goToEditCartRulePage(page) {

  }
}

module.exports = new CartRules();
