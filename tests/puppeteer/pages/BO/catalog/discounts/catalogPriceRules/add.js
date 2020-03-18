require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddCatalogPriceRule extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Catalog Price Rules > Add new •';

    // Selectors
    this.catalogPriceRuleForm = '#specific_price_rule_form';
    this.nameInput = '#name';
    this.currencySelect = '#id_currency';
    this.countrySelect = '#id_country';
    this.groupSelect = '#id_group';
    this.fromQuantityInput = '#from_quantity';
    this.reductionTypeSelect = '#reduction_type';
    this.reductionInput = '#reduction';
    this.reductionTaxSelect = '#reduction_tax';
    this.saveButton = '#specific_price_rule_form_submit_btn';
  }

  /* Methods */
  /**
   * Create/edit price rule
   * @param priceRuleData
   * @returns {Promise<string>}
   */
  async createEditCatalogPriceRule(priceRuleData) {
    await this.setValue(this.nameInput, priceRuleData.name);
    await this.selectByVisibleText(this.currencySelect, priceRuleData.currency);
    await this.selectByVisibleText(this.countrySelect, priceRuleData.country);
    await this.selectByVisibleText(this.groupSelect, priceRuleData.group);
    await this.setValue(this.fromQuantityInput, priceRuleData.fromQuantity.toString());
    await this.selectByVisibleText(this.reductionTypeSelect, priceRuleData.reductionType);
    await this.selectByVisibleText(this.reductionTaxSelect, priceRuleData.reductionTax);
    await this.setValue(this.reductionInput, priceRuleData.reduction.toString());
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
