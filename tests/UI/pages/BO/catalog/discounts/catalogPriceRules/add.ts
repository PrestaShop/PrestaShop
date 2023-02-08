import BOBasePage from '@pages/BO/BObasePage';

import type CatalogPriceRuleData from '@data/faker/catalogPriceRule';

import type {Page} from 'playwright';

/**
 * Add catalog price rule page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddCatalogPriceRule extends BOBasePage {
  public readonly pageTitle: string;

  public readonly editPageTitle: string;

  private readonly catalogPriceRuleForm: string;

  private readonly nameInput: string;

  private readonly currencySelect: string;

  private readonly countrySelect: string;

  private readonly groupSelect: string;

  private readonly fromQuantityInput: string;

  private readonly fromDateInput: string;

  private readonly toDateInput: string;

  private readonly reductionTypeSelect: string;

  private readonly reductionInput: string;

  private readonly reductionTaxSelect: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add price rule page
   */
  constructor() {
    super();

    this.pageTitle = 'Catalog Price Rules > Add new •';
    this.editPageTitle = 'Catalog Price Rules > Edit:';

    // Selectors
    this.catalogPriceRuleForm = '#specific_price_rule_form';
    this.nameInput = '#name';
    this.currencySelect = '#id_currency';
    this.countrySelect = '#id_country';
    this.groupSelect = '#id_group';
    this.fromQuantityInput = '#from_quantity';
    this.fromDateInput = '#from';
    this.toDateInput = '#to';
    this.reductionTypeSelect = '#reduction_type';
    this.reductionInput = '#reduction';
    this.reductionTaxSelect = '#reduction_tax';
    this.saveButton = '#specific_price_rule_form_submit_btn';
  }

  /* Methods */
  /**
   * Create/edit price rule
   * @param page {Page} Browser tab
   * @param priceRuleData {CatalogPriceRuleData} Data to set on new/edit catalog price rule form
   * @returns {Promise<string>}
   */
  async setCatalogPriceRule(page: Page, priceRuleData: CatalogPriceRuleData): Promise<string> {
    await this.setValue(page, this.nameInput, priceRuleData.name);
    await this.selectByVisibleText(page, this.currencySelect, priceRuleData.currency);
    await this.selectByVisibleText(page, this.countrySelect, priceRuleData.country);
    await this.selectByVisibleText(page, this.groupSelect, priceRuleData.group);
    await this.setValue(page, this.fromQuantityInput, priceRuleData.fromQuantity);
    if (priceRuleData.fromDate !== '') {
      await page.type(this.fromDateInput, priceRuleData.fromDate);
    }
    if (priceRuleData.toDate !== '') {
      await page.type(this.toDateInput, priceRuleData.toDate);
    }
    await this.selectByVisibleText(page, this.reductionTypeSelect, priceRuleData.reductionType);
    await this.selectByVisibleText(page, this.reductionTaxSelect, priceRuleData.reductionTax);
    await this.setValue(page, this.reductionInput, priceRuleData.reduction);
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new AddCatalogPriceRule();
