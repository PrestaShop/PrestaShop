require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddCartRule extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Cart Rules > Add new •';

    // Selectors
    this.cartRuleForm = '#cart_rule_form';
    // Information tab
    this.nameInput = ID => `#name_${ID}`;
    this.descriptionTextArea = `${this.cartRuleForm} textarea[name='description']`;
    this.codeInput = '#code';
    this.generateButton = '#cart_rule_informations  a.btn-default';
    // Conditions tab
    this.conditionsTabLink = '#cart_rule_link_conditions';
    this.singleCustomerInput = '#customerFilter';
    this.singleCustomerResultBlock = 'div.ac_results';
    this.singleCustomerResultItem = `${this.singleCustomerResultBlock} ul li`;
    // Actions tab
    this.actionsTabLink = '#cart_rule_link_actions';
    this.freeShippingInput = TOGGLE => `${this.cartRuleForm} label[for='free_shipping_${TOGGLE}']`;
    this.applyDiscountRadioButton = TOGGLE => `${this.cartRuleForm} label[for='apply_discount_${TOGGLE}']`;
    this.reductionPercentInput = '#reduction_percent';
    this.saveAndStayButton = '#desc-cart_rule-save-and-stay';
  }

  /* Methods */
  /**
   * Create/edit cart rule
   * @param page
   * @param cartRuleData
   * @returns {Promise<string>}
   */
  async createEditCartRules(page, cartRuleData) {
    // Fill information form
    await this.setValue(page, this.nameInput(1), cartRuleData.name);
    await this.setValue(page, this.descriptionTextArea, cartRuleData.description);
    if (cartRuleData.code) await this.setValue(page, this.codeInput, cartRuleData.code);
    else await page.click(this.generateButton);
    // Fill conditions form
    await page.click(this.conditionsTabLink);
    await this.setValue(page, this.singleCustomerInput, cartRuleData.customer);
    await this.waitForVisibleSelector(page, `${this.singleCustomerResultBlock}:not([style*='display: none;'])`);
    await page.click(this.singleCustomerResultItem);
    // Fill actions form
    await page.click(this.actionsTabLink);
    await page.click(this.freeShippingInput(cartRuleData.freeShipping));

    if (cartRuleData.percent) await page.click(this.applyDiscountRadioButton('percent'));
    await this.setValue(page, this.reductionPercentInput, cartRuleData.value.toString());

    await this.clickAndWaitForNavigation(page, this.saveAndStayButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new AddCartRule();
