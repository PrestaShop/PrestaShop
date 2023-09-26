// Import pages
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import ProductData from '@data/faker/product';

import type {ProductSpecificPrice} from '@data/types/product';

import type {Page} from 'playwright';

/**
 * Pricing tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class PricingTab extends BOBasePage {
  private readonly pricingTabLink: string;

  private readonly retailPriceInputTaxExcl: string;

  private readonly retailPriceInputTaxIncl: string;

  private readonly taxRuleID: string;

  private readonly taxRuleSelect: string;

  private readonly taxRuleSpan: string;

  private readonly taxRuleList: string;

  private readonly wholesalePriceInput: string;

  private readonly unitPriceInput: string;

  private readonly unityInput: string;

  private readonly onSaleCheckbox: string;

  private readonly productPricingSummarySection: string;

  private readonly priceTaxExcludedValue: string;

  private readonly priceTaxIncludedValue: string;

  private readonly unitPriceValue: string;

  private readonly marginValue: string;

  private readonly marginRateValue: string;

  private readonly wholeSalePriceValue: string;

  private readonly displayRetailPricePerUnit: (toEnable: number) => string;

  private readonly ecotaxInput: string;

  private readonly retailPricePerUnitInputTaxExcl: string;

  private readonly retailPricePerUnitInputTaxIncl: string;

  private readonly addSpecificPriceButton: string;

  private readonly specificPriceModal: string;

  private readonly startingAtInput: string;

  private readonly applyDiscountToInitialPrice: (value: boolean) => string;

  private readonly combinationSelectButton: string;

  private readonly combinationSelectResult: string;

  private readonly combinationToSelectButton: (idCombination: string) => string;

  private readonly applyDiscountOfInput: string;

  private readonly reductionType: string;

  private readonly saveAndPublishButton: string;

  private readonly closeSpecificPriceForm: string;

  private readonly pricingOnSaleCheckBox: string;

  private readonly catalogPriceRulesTable: string;

  private readonly catalogPriceRuleRow: (row: number) => string;

  private readonly catalogPriceRuleRowColumn: (row: number, column: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on pricing tab
   */
  constructor() {
    super();

    // Selectors in pricing tab
    this.pricingTabLink = '#product_pricing-tab-nav';
    // Selectors in retail price section
    this.retailPriceInputTaxExcl = '#product_pricing_retail_price_price_tax_excluded';
    this.retailPriceInputTaxIncl = '#product_pricing_retail_price_price_tax_included';
    this.taxRuleID = 'product_pricing_retail_price_tax_rules_group_id';
    this.taxRuleSelect = `#${this.taxRuleID}`;
    this.taxRuleSpan = `#select2-${this.taxRuleID}-container`;
    this.taxRuleList = `ul#select2-${this.taxRuleID}-results`;
    // Selectors in cost price section
    this.wholesalePriceInput = '#product_pricing_wholesale_price';
    // Selectors in retail price per unit section
    this.displayRetailPricePerUnit = (toEnable: number) => `#product_pricing_disabling_switch_unit_price_${toEnable}`;
    this.retailPricePerUnitInputTaxExcl = '#product_pricing_unit_price_price_tax_excluded';
    this.retailPricePerUnitInputTaxIncl = '#product_pricing_unit_price_price_tax_included';
    this.unitPriceInput = '#product_pricing_unit_price_price_tax_excluded';
    this.unityInput = '#product_pricing_unit_price_unity';
    // Selectors in summary section
    this.productPricingSummarySection = '#product_pricing_summary';
    this.priceTaxExcludedValue = `${this.productPricingSummarySection} div.price-tax-excluded-value`;
    this.priceTaxIncludedValue = `${this.productPricingSummarySection} div.price-tax-included-value`;
    this.unitPriceValue = `${this.productPricingSummarySection} div.unit-price-value`;
    this.marginValue = `${this.productPricingSummarySection} div.margin-value`;
    this.marginRateValue = `${this.productPricingSummarySection} div.margin-rate-value`;
    this.wholeSalePriceValue = `${this.productPricingSummarySection} div.wholesale-price-value`;
    this.onSaleCheckbox = '#product_pricing_on_sale';
    this.ecotaxInput = '#product_pricing_retail_price_ecotax_tax_excluded';
    this.pricingOnSaleCheckBox = '#product_pricing div.form-group.checkbox-widget div.md-checkbox-inline';

    // Selectors in specific Price section
    this.addSpecificPriceButton = '#product_pricing_specific_prices_add_specific_price_btn';
    // Specific Price modal
    this.specificPriceModal = '#modal-specific-price-form';
    this.closeSpecificPriceForm = `${this.specificPriceModal} div.modal-header button.close`;
    // Combination Modal Bloc
    this.combinationSelectButton = '#select2-specific_price_combination_id-container';
    this.combinationSelectResult = '#select2-specific_price_combination_id-results';
    this.combinationToSelectButton = (idCombination: string) => `li.select2-results__option:nth-child(${idCombination})`;
    // Minimum number of units purchased Bloc
    this.startingAtInput = '#specific_price_from_quantity';
    // Impact on price Bloc
    this.applyDiscountToInitialPrice = (value: boolean) => '#specific_price_impact_disabling_switch_reduction_'
      + `${value ? '1' : '0'}`;
    this.applyDiscountOfInput = '#specific_price_impact_reduction_value';
    this.reductionType = '#specific_price_impact_reduction_type';
    // Footer
    this.saveAndPublishButton = `${this.specificPriceModal} div.modal-footer button.btn-confirm-submit`;

    // Selectors in catalog price rules table
    this.catalogPriceRulesTable = '#catalog-price-rules-list-table';
    this.catalogPriceRuleRow = (row: number) => `${this.catalogPriceRulesTable} tbody tr:nth-child(${row})`;
    this.catalogPriceRuleRowColumn = (row: number, column: string) => `${this.catalogPriceRuleRow(row)} td.${column}`;

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
    await this.setRetailPrice(page, true, productData.price);
    // Select tax rule by ID
    await Promise.all([
      this.waitForSelectorAndClick(page, this.taxRuleSpan),
      this.waitForVisibleSelector(page, this.taxRuleList),
    ]);
    await page.locator(`li:has-text('${productData.taxRule}')`).click();
  }

  /**
   * Set tax rule
   * @param page {Page} Browser tab
   * @param taxRule {string} Tax rule to select
   * @returns {Promise<void>}
   */
  async setTaxRule(page: Page, taxRule: string): Promise<void> {
    await Promise.all([
      this.waitForSelectorAndClick(page, this.taxRuleSpan),
      this.waitForVisibleSelector(page, this.taxRuleList),
    ]);
    await page.locator(`li:has-text('${taxRule}')`).click();
  }

  /**
   * Set retail price
   * @param page {Page} Browser tab
   * @param isTaxExcluded {boolean} is Tax Excluded
   * @param price {number} Retail price
   * @returns {Promise<void>}
   */
  async setRetailPrice(page: Page, isTaxExcluded: boolean, price: number): Promise<void> {
    await this.setValue(
      page,
      isTaxExcluded ? this.retailPriceInputTaxExcl : this.retailPriceInputTaxIncl,
      price,
    );
  }

  /**
   * Set cost price
   * @param page {Page} Browser tab
   * @param costPrice {number}
   * @returns {Promise<void>}
   */
  async setCostPrice(page: Page, costPrice: number): Promise<void> {
    await this.setValue(page, this.wholesalePriceInput, costPrice);
  }

  /**
   * Set display retail price per unit
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to check display retail price per unit
   * @returns {Promise<void>}
   */
  async setDisplayRetailPricePerUnit(page: Page, toEnable: true): Promise<void> {
    await this.setChecked(page, this.displayRetailPricePerUnit(toEnable ? 1 : 0));
  }

  /**
   * Set retail price per unit
   * @param page {Page} Browser tab
   * @param isTaxExcluded {boolean} is Tax Excluded
   * @param price {number} Retail price
   * @param unit {string} Unit
   * @returns {Promise<void>}
   */
  async setRetailPricePerUnit(page: Page, isTaxExcluded: boolean, price: number, unit: string): Promise<void> {
    await this.setValue(
      page,
      isTaxExcluded ? this.retailPricePerUnitInputTaxExcl : this.retailPricePerUnitInputTaxIncl,
      price,
    );
    await this.setValue(page, this.unityInput, unit);
  }

  /**
   * Get summary
   * @param page {Page} Browser tab
   * @returns {Promise<object>}
   */
  async getSummary(page: Page) {
    await this.waitForSelectorAndClick(page, this.pricingTabLink);
    return {
      priceTaxExcludedValue: await this.getTextContent(page, this.priceTaxExcludedValue),
      priceTaxIncludedValue: await this.getTextContent(page, this.priceTaxIncludedValue),
      marginValue: await this.getTextContent(page, this.marginValue),
      marginRateValue: await this.getTextContent(page, this.marginRateValue),
      WholesalePriceValue: await this.getTextContent(page, this.wholeSalePriceValue),
    };
  }

  /**
   * Get unit price
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getUnitPriceValue(page: Page): Promise<string> {
    return this.getTextContent(page, this.unitPriceValue);
  }

  /**
   * Set display on sale flag
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async setDisplayOnSaleFlag(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.pricingOnSaleCheckBox);
  }

  /**
   * Click on add specific price button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnAddSpecificPriceButton(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.pricingTabLink);

    await Promise.all([
      page.locator(this.addSpecificPriceButton).click(),
      this.waitForVisibleSelector(page, `${this.specificPriceModal}.show`),
    ]);
  }

  /**
   * Click on edit specific price icon
   * @param page {Page} Browser tab
   * @param row {number} Row to edit
   * @returns {Promise<void>}
   */
  async clickOnEditSpecificPriceIcon(page: Page, row: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.pricingTabLink);

    await Promise.all([
      page.locator(`#specific-prices-list-table tr:nth-child(${row}) td button.js-edit-specific-price-btn`).click(),
      this.waitForVisibleSelector(page, `${this.specificPriceModal}.show`),
    ]);
  }

  /**
   * Product specific price
   * @param page {Page} Browser tab
   * @param specificPriceData {ProductSpecificPrice} Data to set on specific price form
   * @return {Promise<string|null>}
   */
  async setSpecificPrice(page: Page, specificPriceData: ProductSpecificPrice): Promise<string | null> {
    const addSpecificPriceFrame = await page.frame({name: 'modal-specific-price-form-iframe'});

    await this.setValue(addSpecificPriceFrame!, this.startingAtInput, specificPriceData.startingAt);
    await this.setChecked(addSpecificPriceFrame!, this.applyDiscountToInitialPrice(specificPriceData.isApplyDiscount));

    // Choose combinations if exist
    if (specificPriceData.attributes) {
      await addSpecificPriceFrame!.locator(this.combinationSelectButton).click();
      await this.waitForVisibleSelector(addSpecificPriceFrame!, this.combinationSelectResult);
      await this.waitForSelectorAndClick(addSpecificPriceFrame!, this.combinationToSelectButton(specificPriceData.attributes));
    }

    await this.setValue(addSpecificPriceFrame!, this.applyDiscountOfInput, specificPriceData.discount);
    await this.selectByVisibleText(addSpecificPriceFrame!, this.reductionType, specificPriceData.reductionType);

    // Save and get growl message
    await page.locator(this.saveAndPublishButton).click();
    const successMessage = await this.getAlertSuccessBlockParagraphContent(addSpecificPriceFrame!);
    await page.locator(this.closeSpecificPriceForm).click();

    return successMessage;
  }

  /**
   * Delete specific price
   * @param page {Page} Browser tab
   * @param row {number} Row to edit
   * @return {Promise<string|null>}
   */
  async deleteSpecificPrice(page: Page, row: number): Promise<string | null> {
    await page.locator(`#specific-prices-list-table tr:nth-child(${row}) td button.js-delete-specific-price-btn`).click();
    await page.locator('#modal-confirm-delete-combination button.btn-confirm-submit').click();

    return this.getGrowlMessageContent(page);
  }

  /**
   *
   * @param page
   */
  async clickOnShowCatalogPriceRuleButton(page: Page): Promise<void> {
    await page.locator('#product_pricing_show_catalog_price_rules').click();
  }

  async clickOnHideCatalogPriceRulesButton(page: Page): Promise<boolean> {
    await page.locator('#product_pricing_show_catalog_price_rules').click();

    return this.elementVisible(page, '#product_pricing_catalog_price_rules', 1000);
  }

  async clickOnManageCatalogPriceRuleLink(page: Page): Promise<Page> {
    return this.openLinkWithTargetBlank(page, '#product_pricing a[href*=\'AdminSpecificPriceRule\']', 'body');
  }

  async getCatalogPriceRuleData(page: Page, row: number) {
    return {
      id: await this.getTextContent(page, this.catalogPriceRuleRowColumn(row, 'catalog-price-rule-id')),
      name: await this.getTextContent(page, this.catalogPriceRuleRowColumn(row, 'name')),
      currency: await this.getTextContent(page, this.catalogPriceRuleRowColumn(row, 'currency')),
      country: await this.getTextContent(page, this.catalogPriceRuleRowColumn(row, 'country')),
      group: await this.getTextContent(page, this.catalogPriceRuleRowColumn(row, 'group')),
      store: await this.getTextContent(page, this.catalogPriceRuleRowColumn(row, 'shop')),
      discount: await this.getTextContent(page, this.catalogPriceRuleRowColumn(row, 'impact')),
      fromQuantity: await this.getNumberFromText(page, this.catalogPriceRuleRowColumn(row, 'from-qty')),
    };
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
        return this.getAttributeContent(page, this.retailPriceInputTaxExcl, 'value');
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
