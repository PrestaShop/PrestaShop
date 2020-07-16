require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddCatalogPriceRule extends BOBasePage {
  constructor() {
    super();

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
   * @param page
   * @param priceRuleData
   * @returns {Promise<string>}
   */
  async createEditCatalogPriceRule(page, priceRuleData) {
    await this.setValue(page, this.nameInput, priceRuleData.name);
    await this.selectByVisibleText(page, this.currencySelect, priceRuleData.currency);
    await this.selectByVisibleText(page, this.countrySelect, priceRuleData.country);
    await this.selectByVisibleText(page, this.groupSelect, priceRuleData.group);
    await this.setValue(page, this.fromQuantityInput, priceRuleData.fromQuantity.toString());
    await this.selectByVisibleText(page, this.reductionTypeSelect, priceRuleData.reductionType);
    await this.selectByVisibleText(page, this.reductionTaxSelect, priceRuleData.reductionTax);
    await this.setValue(page, this.reductionInput, priceRuleData.reduction.toString());
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new AddCatalogPriceRule();
