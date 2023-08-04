// Import pages
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import ProductData from '@data/faker/product';

import type {Page} from 'playwright';

/**
 * Pricing tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class PricingTab extends BOBasePage {
  private readonly pricingTabLink: string;

  private readonly retailPriceInput: string;

  private readonly taxRuleID: string;

  private readonly taxRuleSelect: string;

  private readonly taxRuleSpan: string;

  private readonly taxRuleList: string;

  private readonly wholesalePriceInput: string;

  private readonly unitPriceInput: string;

  private readonly unityInput: string;

  private readonly onSaleCheckbox: string;

  private readonly ecotaxInput: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on pricing tab
   */
  constructor() {
    super();

    // Selectors in pricing tab
    this.pricingTabLink = '#product_pricing-tab-nav';
    this.retailPriceInput = '#product_pricing_retail_price_price_tax_excluded';
    this.taxRuleID = 'product_pricing_retail_price_tax_rules_group_id';
    this.taxRuleSelect = `#${this.taxRuleID}`;
    this.taxRuleSpan = `#select2-${this.taxRuleID}-container`;
    this.taxRuleList = `ul#select2-${this.taxRuleID}-results`;
    this.wholesalePriceInput = '#product_pricing_wholesale_price';
    this.unitPriceInput = '#product_pricing_unit_price_price_tax_excluded';
    this.unityInput = '#product_pricing_unit_price_unity';
    this.onSaleCheckbox = '#product_pricing_on_sale';
    this.ecotaxInput = '#product_pricing_retail_price_ecotax_tax_excluded';
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
  async setProductPricing(page: Page, productData: ProductData): Promise<void> {
    await this.waitForSelectorAndClick(page, this.pricingTabLink);
    await this.setValue(page, this.retailPriceInput, productData.price);
    // Select tax rule by ID
    await Promise.all([
      this.waitForSelectorAndClick(page, this.taxRuleSpan),
      this.waitForVisibleSelector(page, this.taxRuleList),
    ]);
    await page.locator(`li:has-text('${productData.taxRule}')`).click();
  }

  /**
   * Returns the value of a form element
   * @param page {Page}
   * @param inputName {string}
   */
  async getValue(page: Page, inputName: string): Promise<string> {
    switch (inputName) {
      case 'ecotax':
        return this.getAttributeContent(page, this.ecotaxInput, 'value');
      case 'id_tax_rules_group':
        return page.$eval(this.taxRuleSelect, (node: HTMLSelectElement) => node.value);
      case 'on_sale':
        return (await this.isChecked(page, this.onSaleCheckbox)) ? '1' : '0';
      case 'price':
        return this.getAttributeContent(page, this.retailPriceInput, 'value');
      case 'unit_price':
        return this.getAttributeContent(page, this.unitPriceInput, 'value');
      case 'unity':
        return this.getAttributeContent(page, this.unityInput, 'value');
      case 'wholesale_price':
        return this.getAttributeContent(page, this.wholesalePriceInput, 'value');
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }
}

export default new PricingTab();
