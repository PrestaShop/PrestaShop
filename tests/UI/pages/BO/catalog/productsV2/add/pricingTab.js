require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Pricing tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class PricingTab extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on pricing tab
   */
  constructor() {
    super();

    // Selectors in pricing tab
    this.pricingTabLink = '#product_pricing-tab-nav';
    this.retailPriceInput = '#product_pricing_retail_price_price_tax_excluded';
    this.taxRuleSpan = '#select2-product_pricing_retail_price_tax_rules_group_id-container';
    this.taxRuleList = 'ul#select2-product_pricing_retail_price_tax_rules_group_id-results';
  }

  /*
  Methods
   */

  /**
   * Set product pricing
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in pricing form
   * @returns {Promise<void>}
   */
  async setProductPricing(page, productData) {
    await this.waitForSelectorAndClick(page, this.pricingTabLink);
    await this.setValue(page, this.retailPriceInput, productData.price);
    // Select tax rule by ID
    await Promise.all([
      this.waitForSelectorAndClick(page, this.taxRuleSpan),
      this.waitForVisibleSelector(page, this.taxRuleList),
    ]);
    await page.locator(`li:has-text('${productData.taxRule}')`).click();
  }
}

module.exports = new PricingTab();
