require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddCartRule extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Cart Rules > Add new •';

    // Selectors
    this.cartRuleForm = '#cart_rule_form';
    // Information tab
    this.nameInput = ID => `#name_${ID}`;
    this.descriptionTextArea = `${this.cartRuleForm} textarea[name='description']`;
    this.codeInput = '#code';
    // Conditions tab
    this.conditionsTabLink = '#cart_rule_link_conditions';
    this.singleCustomerInput = '#customerFilter';
    this.singleCustomerResult = 'div.ac_results ul li';
    // Actions tab
    this.actionsTabLink = '#cart_rule_link_actions';
    this.freeShippingInput = TOGGLE => `${this.cartRuleForm} label[for='free_shipping_${TOGGLE}']`;
    this.saveAndStayButton = '#desc-cart_rule-save-and-stay';
  }

  /* Methods */
  /**
   * Create/edit cart rule
   * @param cartRuleData
   * @returns {Promise<string>}
   */
  async createEditCartRules(cartRuleData) {
    await this.setValue(this.nameInput(1), cartRuleData.name);
    await this.setValue(this.descriptionTextArea, cartRuleData.description);
    await this.setValue(this.codeInput, cartRuleData.code);
    await this.page.click(this.conditionsTabLink);
    await this.setValue(this.singleCustomerInput, cartRuleData.customer);
    await this.waitForSelectorAndClick(this.singleCustomerResult);
    await this.page.click(this.actionsTabLink);
    await this.page.click(this.freeShippingInput(cartRuleData.freeShipping));
    await this.clickAndWaitForNavigation(this.saveAndStayButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
